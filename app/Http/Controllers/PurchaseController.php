<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Purchase;
use App\Models\PurchaseBatch;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = $this->scopeToAccessibleBranches(
            Purchase::with(['supplier', 'branch', 'batches.product'])->orderByDesc('purchase_date')
        )->get();

        return view('purchases.index', [
            'purchases' => $purchases,
            'suppliers' => Supplier::orderBy('name')->get(),
            'branches' => $this->accessibleBranches()->get(),
            'products' => Product::orderBy('name')->get(),
            'purchaseSummary' => [
                'count' => $purchases->count(),
                'total' => (float) $purchases->sum('total'),
                'today' => (float) $purchases->where('purchase_date', now()->toDateString())->sum('total'),
            ],
        ]);
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'branch_id' => 'required|exists:branches,id',
            'purchase_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        $this->ensureBranchAccess((int) $request->branch_id);

        $total = $request->quantity * $request->unit_cost;
        $tax = $request->tax ?? 0;

        $purchase = Purchase::create([
            'reference' => 'PUR/' . now()->format('Ymd') . '/' . str_pad((string) (Purchase::count() + 1), 4, '0', STR_PAD_LEFT),
            'supplier_id' => $request->supplier_id,
            'branch_id' => $request->branch_id,
            'purchase_date' => $request->purchase_date,
            'subtotal' => $total,
            'tax' => $tax,
            'total' => $total + $tax,
            'status' => 'received',
            'notes' => $request->notes,
        ]);

        $batch = PurchaseBatch::create([
            'purchase_id' => $purchase->id,
            'product_id' => $request->product_id,
            'branch_id' => $request->branch_id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $total,
            'received_at' => $request->purchase_date,
        ]);

        $inventoryService->addPurchaseBatch($batch);

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dicatat.');
    }
}
