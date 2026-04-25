<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index', [
            'sales' => $this->scopeToAccessibleBranches(
                Sale::with(['branch', 'customer', 'shift'])->orderByDesc('created_at')
            )->get(),
        ]);
    }

    public function create()
    {
        $branchIds = $this->accessibleBranchIds();
        $products = Product::with(['branches.branch', 'category'])
            ->where('is_active', true)
            ->whereHas('branches', fn ($query) => $query->whereIn('branch_id', $branchIds))
            ->orderBy('name')
            ->get();

        $catalog = $products->map(function (Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'image_url' => $product->image_url,
                'category' => $product->category?->name ?? 'Umum',
                'description' => $product->description,
                'base_price' => (float) $product->base_price,
                'branches' => $product->branches->mapWithKeys(function ($branch) {
                    return [
                        $branch->branch_id => [
                            'price' => (float) $branch->selling_price,
                            'stock' => (float) $branch->stock,
                            'branch_name' => $branch->branch?->name,
                        ],
                    ];
                })->toArray(),
            ];
        })->values();

        return view('sales.create', [
            'products' => $products,
            'catalog' => $catalog,
            'customers' => Customer::orderBy('name')->get(),
            'branches' => $this->accessibleBranches()->get(),
            'cashiers' => auth()->user()->hasRole('cashier')
                ? User::whereKey(auth()->id())->get()
                : User::where('role', 'cashier')->whereIn('branch_id', $branchIds)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'cashier_id' => 'nullable|exists:users,id',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items_json' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $items = [];

        if (! empty($validated['items_json'])) {
            $items = json_decode($validated['items_json'], true) ?: [];
        }

        if (empty($items) && $request->filled('product_id')) {
            $items[] = [
                'product_id' => (int) $request->product_id,
                'quantity' => (int) $request->quantity,
                'unit_price' => (float) $request->unit_price,
                'discount' => 0,
                'total' => (float) $request->quantity * (float) $request->unit_price,
            ];
        }

        if (empty($items)) {
            return back()->withErrors(['items_json' => 'Keranjang penjualan masih kosong.'])->withInput();
        }

        try {
            $this->ensureBranchAccess((int) $validated['branch_id']);
            $branch = Branch::findOrFail($validated['branch_id']);
            $discount = (float) ($validated['discount'] ?? 0);
            $tax = (float) ($validated['tax'] ?? 0);
            $selectedCashierId = $validated['cashier_id'] ?? null;
            $cashierId = auth()->user()->hasRole('cashier') ? auth()->id() : ($selectedCashierId ?: auth()->id());
            $shouldValidateCashier = auth()->user()->hasRole('cashier') || ! empty($selectedCashierId);

            if ($shouldValidateCashier) {
                $cashier = User::whereKey($cashierId)
                    ->where('role', 'cashier')
                    ->whereIn('branch_id', $this->accessibleBranchIds())
                    ->first();

                if (! $cashier) {
                    throw new \InvalidArgumentException('Kasir yang dipilih tidak valid untuk cabang atau akses Anda.');
                }

                if ((int) $cashier->branch_id !== (int) $validated['branch_id']) {
                    throw new \InvalidArgumentException('Kasir yang dipilih tidak terhubung ke cabang transaksi ini.');
                }
            }

            $activeShift = $shouldValidateCashier
                ? CashierShift::query()
                    ->where('user_id', $cashierId)
                    ->where('branch_id', $validated['branch_id'])
                    ->where('status', 'open')
                    ->latest('started_at')
                    ->first()
                : null;

            if (auth()->user()->hasRole('cashier') && ! $activeShift) {
                throw new \InvalidArgumentException('Shift kasir belum dibuka. Buka shift terlebih dahulu sebelum melakukan transaksi.');
            }

            $productIds = collect($items)->pluck('product_id')->filter()->unique()->values();
            $products = Product::with(['branches' => function ($query) use ($validated) {
                $query->where('branch_id', $validated['branch_id']);
            }])->whereIn('id', $productIds)->get()->keyBy('id');

            $normalizedItems = collect($items)->map(function (array $item) use ($products) {
                $product = $products->get((int) ($item['product_id'] ?? 0));

                if (! $product) {
                    throw new \InvalidArgumentException('Salah satu produk tidak ditemukan.');
                }

                $branchProduct = $product->branches->first();
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $unitPrice = (float) ($item['unit_price'] ?? $branchProduct?->selling_price ?? $product->base_price ?? 0);
                $lineDiscount = (float) ($item['discount'] ?? 0);
                $lineTotal = max(0, ($quantity * $unitPrice) - $lineDiscount);

                if (! $branchProduct) {
                    throw new \InvalidArgumentException("Produk {$product->name} belum tersedia untuk cabang yang dipilih.");
                }

                if ((float) $branchProduct->stock < $quantity) {
                    throw new \InvalidArgumentException("Stok {$product->name} tidak mencukupi untuk transaksi ini.");
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $lineDiscount,
                    'total' => $lineTotal,
                ];
            });

            $subtotal = (float) $normalizedItems->sum(fn (array $item) => $item['quantity'] * $item['unit_price']);
            $itemDiscount = (float) $normalizedItems->sum('discount');
            $totalDiscount = $discount + $itemDiscount;
            $total = max(0, $subtotal - $totalDiscount + $tax);
            $paidAmount = (float) ($validated['paid_amount'] ?? $total);

            if ($paidAmount < $total) {
                throw new \InvalidArgumentException('Jumlah bayar tidak boleh lebih kecil dari total transaksi.');
            }

            $invoicePrefix = (string) (Setting::where('key', 'invoice_prefix')->value('value') ?: 'PST');

            DB::transaction(function () use (
                $validated,
                $branch,
                $normalizedItems,
                $subtotal,
                $totalDiscount,
                $tax,
                $total,
                $paidAmount,
                $cashierId,
                $activeShift,
                $invoicePrefix,
                $inventoryService
            ) {
                $sale = Sale::create([
                    'invoice' => $invoicePrefix . '/' . now()->format('Ymd') . '/' . str_pad((string) (Sale::count() + 1), 4, '0', STR_PAD_LEFT),
                    'branch_id' => $validated['branch_id'],
                    'customer_id' => $validated['customer_id'] ?? null,
                    'shift_id' => $activeShift?->id,
                    'created_by' => $cashierId,
                    'subtotal' => $subtotal,
                    'discount' => $totalDiscount,
                    'tax' => $tax,
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'status' => 'completed',
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($normalizedItems as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product']->id,
                        'branch_id' => $validated['branch_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'],
                        'tax' => 0,
                        'total' => $item['total'],
                    ]);

                    $inventoryService->registerSaleItem($item['product'], $branch, $item['quantity']);
                }

                if (! empty($validated['customer_id'])) {
                    $customer = Customer::findOrFail($validated['customer_id']);
                    $pointsEarned = round($total / 10000, 2);
                    $customer->points_balance += $pointsEarned;
                    $customer->save();
                    $customer->pointHistories()->create([
                        'branch_id' => $validated['branch_id'],
                        'points' => $pointsEarned,
                        'balance' => $customer->points_balance,
                        'type' => 'earn',
                        'description' => "Poin dari penjualan #{$sale->invoice}",
                    ]);
                }
            });

            return redirect()->route('sales.index')->with('success', 'Transaksi penjualan berhasil disimpan.');
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['items_json' => $exception->getMessage()])->withInput();
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors(['items_json' => 'Transaksi gagal diproses. Silakan coba lagi.'])->withInput();
        }
    }

    public function receipt(Sale $sale)
    {
        $this->ensureBranchAccess((int) $sale->branch_id);
        $sale->load(['branch', 'customer', 'items.product']);

        $settings = \App\Models\Setting::whereIn('key', [
            'store_name',
            'receipt_footer',
            'invoice_prefix',
            'printer_paper_width',
            'printer_show_logo',
        ])->pluck('value', 'key');

        return view('sales.receipt', [
            'sale' => $sale,
            'receiptSettings' => [
                'store_name' => $settings['store_name'] ?? 'Kasir Pusat Store',
                'receipt_footer' => $settings['receipt_footer'] ?? 'Terima kasih telah berbelanja',
                'paper_width' => $settings['printer_paper_width'] ?? '80mm',
                'show_logo' => (string) ($settings['printer_show_logo'] ?? '0') === '1',
            ],
        ]);
    }
}
