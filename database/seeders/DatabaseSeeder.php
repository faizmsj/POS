<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerPointHistory;
use App\Models\PPOBProduct;
use App\Models\PPOBProvider;
use App\Models\PPOBTransaction;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Purchase;
use App\Models\PurchaseBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $branches = collect([
            [
                'code' => 'KSP001',
                'name' => 'Kasir Pusat',
                'address' => 'Jalan Pahlawan No. 1',
                'phone' => '+6281234567890',
            ],
            [
                'code' => 'KSP002',
                'name' => 'Kasir Selatan',
                'address' => 'Jalan Melati No. 22',
                'phone' => '+628123450000',
            ],
            [
                'code' => 'KSP003',
                'name' => 'Kasir Utara',
                'address' => 'Jalan Mawar No. 8',
                'phone' => '+628123450123',
            ],
        ])->mapWithKeys(function (array $branch) {
            $model = Branch::updateOrCreate([
                'code' => $branch['code'],
            ], [
                'name' => $branch['name'],
                'address' => $branch['address'],
                'phone' => $branch['phone'],
                'configuration' => ['timezone' => 'Asia/Jakarta'],
                'is_active' => true,
            ]);

            return [$branch['code'] => $model];
        });

        $users = collect([
            [
                'email' => 'owner@example.com',
                'name' => 'Owner Demo',
                'role' => 'owner',
                'branch_code' => 'KSP001',
            ],
            [
                'email' => 'admin@example.com',
                'name' => 'Administrator Pusat',
                'role' => 'admin',
                'branch_code' => 'KSP001',
            ],
            [
                'email' => 'manager@example.com',
                'name' => 'Manager Operasional',
                'role' => 'manager',
                'branch_code' => 'KSP002',
            ],
            [
                'email' => 'cashier@example.com',
                'name' => 'Kasir Pusat',
                'role' => 'cashier',
                'branch_code' => 'KSP001',
            ],
            [
                'email' => 'cashier2@example.com',
                'name' => 'Kasir Selatan',
                'role' => 'cashier',
                'branch_code' => 'KSP002',
            ],
            [
                'email' => 'cashier3@example.com',
                'name' => 'Kasir Utara',
                'role' => 'cashier',
                'branch_code' => 'KSP003',
            ],
        ])->mapWithKeys(function (array $user) use ($branches) {
            $model = User::updateOrCreate([
                'email' => $user['email'],
            ], [
                'name' => $user['name'],
                'branch_id' => $branches[$user['branch_code']]->id,
                'role' => $user['role'],
                'password' => bcrypt('password'),
            ]);

            return [$user['email'] => $model];
        });

        $suppliers = collect([
            [
                'email' => 'supplier@example.com',
                'name' => 'Supplier Utama',
                'phone' => '+6280987654321',
                'address' => 'Gudang Utama',
            ],
            [
                'email' => 'frozen@example.com',
                'name' => 'Frozen Food Nusantara',
                'phone' => '+628088888888',
                'address' => 'Distribusi Kota',
            ],
            [
                'email' => 'beverage@example.com',
                'name' => 'Beverage Partner',
                'phone' => '+628077777777',
                'address' => 'Pusat Minuman',
            ],
        ])->mapWithKeys(function (array $supplier) {
            $model = Supplier::updateOrCreate([
                'email' => $supplier['email'],
            ], $supplier);

            return [$supplier['email'] => $model];
        });

        $categories = collect();

        if (Schema::hasTable('categories')) {
            $categories = collect([
                ['name' => 'Makanan', 'slug' => 'makanan'],
                ['name' => 'Minuman', 'slug' => 'minuman'],
                ['name' => 'Sembako', 'slug' => 'sembako'],
                ['name' => 'Snack', 'slug' => 'snack'],
            ])->mapWithKeys(function (array $category) {
                $model = Category::updateOrCreate([
                    'slug' => $category['slug'],
                ], [
                    'name' => $category['name'],
                    'is_active' => true,
                ]);

                return [$category['slug'] => $model];
            });
        }

        $placeholder = fn (string $name, string $category): string => 'data:image/svg+xml;charset=UTF-8,'.rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 420"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#2563eb"/><stop offset="60%" stop-color="#06b6d4"/><stop offset="100%" stop-color="#0f172a"/></linearGradient></defs><rect width="600" height="420" rx="36" fill="url(#g)"/><circle cx="485" cy="92" r="74" fill="rgba(255,255,255,0.14)"/><circle cx="112" cy="334" r="96" fill="rgba(255,255,255,0.08)"/><text x="300" y="188" text-anchor="middle" font-size="36" font-family="Arial, sans-serif" font-weight="700" fill="#ffffff">'.$category.'</text><text x="300" y="246" text-anchor="middle" font-size="58" font-family="Arial, sans-serif" font-weight="700" fill="#ffffff">'.$name.'</text></svg>'
        );

        $products = collect([
            ['sku' => 'FOOD-001', 'name' => 'Nasi Goreng Spesial', 'barcode' => '1234567890123', 'cost_price' => 12000, 'base_price' => 21000, 'category_slug' => 'makanan'],
            ['sku' => 'FOOD-002', 'name' => 'Mie Goreng', 'barcode' => '1234567890124', 'cost_price' => 4500, 'base_price' => 7500, 'category_slug' => 'makanan'],
            ['sku' => 'FOOD-003', 'name' => 'Ayam Crispy', 'barcode' => '1234567890128', 'cost_price' => 15000, 'base_price' => 24000, 'category_slug' => 'makanan'],
            ['sku' => 'DRINK-001', 'name' => 'Es Teh Manis', 'barcode' => '1234567890125', 'cost_price' => 4000, 'base_price' => 8000, 'category_slug' => 'minuman'],
            ['sku' => 'DRINK-002', 'name' => 'Air Mineral 600ml', 'barcode' => '1234567890126', 'cost_price' => 3500, 'base_price' => 6000, 'category_slug' => 'minuman'],
            ['sku' => 'DRINK-003', 'name' => 'Kopi Susu', 'barcode' => '1234567890129', 'cost_price' => 8000, 'base_price' => 15000, 'category_slug' => 'minuman'],
            ['sku' => 'BASIC-001', 'name' => 'Beras 5kg', 'barcode' => '1234567890127', 'cost_price' => 62000, 'base_price' => 69000, 'category_slug' => 'sembako'],
            ['sku' => 'SNACK-001', 'name' => 'Keripik Kentang', 'barcode' => '1234567890130', 'cost_price' => 7000, 'base_price' => 12000, 'category_slug' => 'snack'],
        ])->map(function (array $product) use ($categories, $placeholder) {
            return Product::updateOrCreate([
                'barcode' => $product['barcode'],
            ], [
                'sku' => $product['sku'],
                'name' => $product['name'],
                'barcode' => $product['barcode'],
                'category_id' => $categories->get($product['category_slug'])?->id,
                'cost_price' => $product['cost_price'],
                'base_price' => $product['base_price'],
                'description' => 'Produk demo untuk operasional POS.',
                'meta' => ['image_url' => $placeholder($product['name'], strtoupper($product['category_slug']))],
                'is_active' => true,
            ]);
        })->keyBy('sku');

        foreach ($branches->values() as $branchIndex => $branch) {
            foreach ($products->values() as $productIndex => $product) {
                $price = (float) $product->base_price + ($branchIndex * 500);
                ProductBranch::updateOrCreate([
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                ], [
                    'stock' => 18 + ($productIndex * 6) + ($branchIndex * 2),
                    'selling_price' => $price,
                    'margin_percent' => $price > 0 ? round((($price - (float) $product->cost_price) / $price) * 100, 2) : 0,
                    'is_active' => true,
                ]);
            }
        }

        $customers = collect([
            [
                'code' => 'CUS-001',
                'name' => 'Pelanggan Biasa',
                'email' => 'customer@example.com',
                'phone' => '+628111222333',
                'points_balance' => 0,
                'loyalty_tier' => 'regular',
            ],
            [
                'code' => 'CUS-002',
                'name' => 'Member Gold',
                'email' => 'gold@example.com',
                'phone' => '+628111222334',
                'points_balance' => 120,
                'loyalty_tier' => 'gold',
            ],
            [
                'code' => 'CUS-003',
                'name' => 'Member Platinum',
                'email' => 'platinum@example.com',
                'phone' => '+628111222335',
                'points_balance' => 260,
                'loyalty_tier' => 'platinum',
            ],
        ])->mapWithKeys(function (array $customer) {
            $model = Customer::updateOrCreate([
                'code' => $customer['code'],
            ], $customer);

            return [$customer['code'] => $model];
        });

        $provider = PPOBProvider::updateOrCreate([
            'code' => 'PPOB-A',
        ], [
            'name' => 'Provider PPOB',
            'api_endpoint' => 'https://api.example.com',
            'credentials' => ['api_key' => 'secret'],
            'is_enabled' => true,
        ]);

        $ppobProducts = collect([
            [
                'code' => 'PULSA-10K',
                'name' => 'Pulsa 10K',
                'category' => 'Pulsa',
                'cost' => 9000,
                'price' => 10000,
                'margin_percent' => 11.11,
            ],
            [
                'code' => 'DATA-5GB',
                'name' => 'Paket Data 5GB',
                'category' => 'Data',
                'cost' => 23000,
                'price' => 25000,
                'margin_percent' => 8,
            ],
            [
                'code' => 'TOKEN-20K',
                'name' => 'Token PLN 20K',
                'category' => 'PLN',
                'cost' => 19500,
                'price' => 21000,
                'margin_percent' => 7.69,
            ],
        ])->mapWithKeys(function (array $product) use ($provider) {
            $model = PPOBProduct::updateOrCreate([
                'code' => $product['code'],
            ], [
                'provider_id' => $provider->id,
                'name' => $product['name'],
                'category' => $product['category'],
                'cost' => $product['cost'],
                'price' => $product['price'],
                'margin_percent' => $product['margin_percent'],
                'is_active' => true,
            ]);

            return [$product['code'] => $model];
        });

        foreach ([
            ['key' => 'store_name', 'value' => 'Kasir Pusat Store', 'group' => 'Umum'],
            ['key' => 'invoice_prefix', 'value' => 'PST', 'group' => 'POS'],
            ['key' => 'default_tax_percent', 'value' => '11', 'group' => 'POS'],
            ['key' => 'loyalty_enabled', 'value' => '1', 'group' => 'Loyalty'],
            ['key' => 'points_per_amount', 'value' => '1000', 'group' => 'Loyalty'],
            ['key' => 'points_redeem_minimum', 'value' => '100', 'group' => 'Loyalty'],
            ['key' => 'points_redeem_value', 'value' => '1000', 'group' => 'Loyalty'],
            ['key' => 'ppob_markup_percent', 'value' => '5', 'group' => 'PPOB'],
            ['key' => 'printer_name', 'value' => 'POS-Printer-01', 'group' => 'Printer'],
            ['key' => 'printer_paper_width', 'value' => '80mm', 'group' => 'Printer'],
            ['key' => 'printer_copies', 'value' => '1', 'group' => 'Printer'],
            ['key' => 'printer_show_logo', 'value' => '0', 'group' => 'Printer'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja', 'group' => 'Printer'],
            ['key' => 'label_paper_size', 'value' => 'A4 12 Label', 'group' => 'Printer'],
            ['key' => 'label_columns', 'value' => '3', 'group' => 'Printer'],
            ['key' => 'label_show_store_name', 'value' => '1', 'group' => 'Printer'],
        ] as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        $purchaseSpecs = [
            [
                'reference' => 'PUR/DEMO/0001',
                'supplier' => 'supplier@example.com',
                'branch' => 'KSP001',
                'product' => 'FOOD-001',
                'qty' => 25,
                'unit_cost' => 12000,
                'date' => $now->copy()->subDays(4),
            ],
            [
                'reference' => 'PUR/DEMO/0002',
                'supplier' => 'beverage@example.com',
                'branch' => 'KSP002',
                'product' => 'DRINK-003',
                'qty' => 20,
                'unit_cost' => 8000,
                'date' => $now->copy()->subDays(3),
            ],
            [
                'reference' => 'PUR/DEMO/0003',
                'supplier' => 'frozen@example.com',
                'branch' => 'KSP003',
                'product' => 'FOOD-003',
                'qty' => 18,
                'unit_cost' => 15000,
                'date' => $now->copy()->subDays(2),
            ],
        ];

        foreach ($purchaseSpecs as $spec) {
            $subtotal = $spec['qty'] * $spec['unit_cost'];
            $purchase = Purchase::updateOrCreate([
                'reference' => $spec['reference'],
            ], [
                'supplier_id' => $suppliers[$spec['supplier']]->id,
                'branch_id' => $branches[$spec['branch']]->id,
                'purchase_date' => $spec['date']->toDateString(),
                'subtotal' => $subtotal,
                'tax' => 0,
                'total' => $subtotal,
                'status' => 'received',
                'notes' => 'Seed pembelian demo '.$spec['branch'],
            ]);

            PurchaseBatch::updateOrCreate([
                'purchase_id' => $purchase->id,
                'product_id' => $products[$spec['product']]->id,
                'branch_id' => $branches[$spec['branch']]->id,
            ], [
                'quantity' => $spec['qty'],
                'unit_cost' => $spec['unit_cost'],
                'total_cost' => $subtotal,
                'received_at' => $spec['date']->toDateString(),
            ]);
        }

        $shiftSpecs = [
            [
                'user' => 'cashier@example.com',
                'branch' => 'KSP001',
                'started_at' => $now->copy()->startOfDay()->addHours(8),
                'opening_balance' => 250000,
                'closing_balance' => null,
                'cash_in' => 35000,
                'cash_out' => 7000,
                'ended_at' => null,
                'status' => 'open',
            ],
            [
                'user' => 'cashier2@example.com',
                'branch' => 'KSP002',
                'started_at' => $now->copy()->subDay()->startOfDay()->addHours(9),
                'opening_balance' => 300000,
                'closing_balance' => 428000,
                'cash_in' => 50000,
                'cash_out' => 12000,
                'ended_at' => $now->copy()->subDay()->startOfDay()->addHours(18),
                'status' => 'closed',
            ],
            [
                'user' => 'cashier3@example.com',
                'branch' => 'KSP003',
                'started_at' => $now->copy()->subDays(2)->startOfDay()->addHours(8),
                'opening_balance' => 275000,
                'closing_balance' => 390000,
                'cash_in' => 42000,
                'cash_out' => 9000,
                'ended_at' => $now->copy()->subDays(2)->startOfDay()->addHours(17),
                'status' => 'closed',
            ],
        ];

        $shifts = collect($shiftSpecs)->mapWithKeys(function (array $shift) use ($users, $branches) {
            $model = CashierShift::updateOrCreate([
                'user_id' => $users[$shift['user']]->id,
                'branch_id' => $branches[$shift['branch']]->id,
                'started_at' => $shift['started_at'],
            ], [
                'opening_balance' => $shift['opening_balance'],
                'closing_balance' => $shift['closing_balance'],
                'cash_in' => $shift['cash_in'],
                'cash_out' => $shift['cash_out'],
                'ended_at' => $shift['ended_at'],
                'status' => $shift['status'],
            ]);

            return [$shift['user'].'-'.$shift['branch'] => $model];
        });

        $saleSpecs = [
            [
                'invoice' => 'PST/DEMO/0001',
                'branch' => 'KSP001',
                'customer' => 'CUS-001',
                'cashier' => 'cashier@example.com',
                'shift' => 'cashier@example.com-KSP001',
                'date' => $now->copy()->subDay(),
                'discount' => 1000,
                'tax' => 0,
                'paid_amount' => 30000,
                'notes' => 'Seed penjualan demo',
                'items' => [
                    ['product' => 'FOOD-001', 'qty' => 1, 'price' => 21000, 'discount' => 1000],
                    ['product' => 'DRINK-001', 'qty' => 1, 'price' => 8000, 'discount' => 0],
                ],
            ],
            [
                'invoice' => 'PST/DEMO/0002',
                'branch' => 'KSP001',
                'customer' => 'CUS-002',
                'cashier' => 'cashier@example.com',
                'shift' => 'cashier@example.com-KSP001',
                'date' => $now->copy()->subHours(6),
                'discount' => 0,
                'tax' => 1500,
                'paid_amount' => 46500,
                'notes' => 'Transaksi makan siang',
                'items' => [
                    ['product' => 'FOOD-002', 'qty' => 2, 'price' => 7500, 'discount' => 0],
                    ['product' => 'FOOD-003', 'qty' => 1, 'price' => 24000, 'discount' => 0],
                    ['product' => 'DRINK-003', 'qty' => 1, 'price' => 15000, 'discount' => 0],
                ],
            ],
            [
                'invoice' => 'PST/DEMO/0003',
                'branch' => 'KSP002',
                'customer' => 'CUS-003',
                'cashier' => 'cashier2@example.com',
                'shift' => 'cashier2@example.com-KSP002',
                'date' => $now->copy()->subDays(2),
                'discount' => 2000,
                'tax' => 0,
                'paid_amount' => 34000,
                'notes' => 'Promo member',
                'items' => [
                    ['product' => 'SNACK-001', 'qty' => 1, 'price' => 12500, 'discount' => 0],
                    ['product' => 'DRINK-003', 'qty' => 2, 'price' => 15500, 'discount' => 2000],
                ],
            ],
            [
                'invoice' => 'PST/DEMO/0004',
                'branch' => 'KSP003',
                'customer' => 'CUS-001',
                'cashier' => 'cashier3@example.com',
                'shift' => 'cashier3@example.com-KSP003',
                'date' => $now->copy()->subDays(3),
                'discount' => 0,
                'tax' => 0,
                'paid_amount' => 48000,
                'notes' => 'Pembelian keluarga',
                'items' => [
                    ['product' => 'FOOD-003', 'qty' => 1, 'price' => 25000, 'discount' => 0],
                    ['product' => 'DRINK-001', 'qty' => 1, 'price' => 8500, 'discount' => 0],
                    ['product' => 'SNACK-001', 'qty' => 1, 'price' => 12500, 'discount' => 0],
                ],
            ],
        ];

        foreach ($saleSpecs as $spec) {
            $subtotal = collect($spec['items'])->sum(fn (array $item) => $item['qty'] * $item['price']);
            $lineDiscount = collect($spec['items'])->sum('discount');
            $totalDiscount = $spec['discount'] + $lineDiscount;
            $total = $subtotal - $totalDiscount + $spec['tax'];

            $sale = Sale::updateOrCreate([
                'invoice' => $spec['invoice'],
            ], [
                'branch_id' => $branches[$spec['branch']]->id,
                'customer_id' => $customers[$spec['customer']]->id,
                'shift_id' => $shifts[$spec['shift']]->id,
                'created_by' => $users[$spec['cashier']]->id,
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'tax' => $spec['tax'],
                'total' => $total,
                'paid_amount' => $spec['paid_amount'],
                'status' => 'completed',
                'notes' => $spec['notes'],
                'created_at' => $spec['date'],
                'updated_at' => $spec['date'],
            ]);

            foreach ($spec['items'] as $itemIndex => $item) {
                SaleItem::updateOrCreate([
                    'sale_id' => $sale->id,
                    'product_id' => $products[$item['product']]->id,
                    'branch_id' => $branches[$spec['branch']]->id,
                ], [
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount' => $item['discount'],
                    'tax' => 0,
                    'purchase_batch_id' => PurchaseBatch::where('product_id', $products[$item['product']]->id)
                        ->where('branch_id', $branches[$spec['branch']]->id)
                        ->value('id'),
                    'total' => ($item['qty'] * $item['price']) - $item['discount'],
                    'created_at' => $spec['date']->copy()->addMinutes($itemIndex),
                    'updated_at' => $spec['date']->copy()->addMinutes($itemIndex),
                ]);
            }
        }

        foreach ([
            [
                'customer' => 'CUS-002',
                'branch' => 'KSP001',
                'points' => 15,
                'balance' => 135,
                'description' => 'Poin dari transaksi promo awal bulan',
                'created_at' => $now->copy()->subDays(6),
            ],
            [
                'customer' => 'CUS-003',
                'branch' => 'KSP002',
                'points' => 30,
                'balance' => 290,
                'description' => 'Poin loyalitas pelanggan aktif',
                'created_at' => $now->copy()->subDays(2),
            ],
        ] as $history) {
            CustomerPointHistory::updateOrCreate([
                'customer_id' => $customers[$history['customer']]->id,
                'branch_id' => $branches[$history['branch']]->id,
                'description' => $history['description'],
            ], [
                'points' => $history['points'],
                'balance' => $history['balance'],
                'type' => 'earn',
                'created_at' => $history['created_at'],
                'updated_at' => $history['created_at'],
            ]);
        }

        foreach ([
            [
                'ref' => 'PPOB-DEMO-0001',
                'product' => 'PULSA-10K',
                'branch' => 'KSP001',
                'cashier' => 'cashier@example.com',
                'status' => 'completed',
                'created_at' => $now->copy()->subHours(5),
            ],
            [
                'ref' => 'PPOB-DEMO-0002',
                'product' => 'DATA-5GB',
                'branch' => 'KSP002',
                'cashier' => 'cashier2@example.com',
                'status' => 'pending',
                'created_at' => $now->copy()->subDay(),
            ],
            [
                'ref' => 'PPOB-DEMO-0003',
                'product' => 'TOKEN-20K',
                'branch' => 'KSP003',
                'cashier' => 'cashier3@example.com',
                'status' => 'success',
                'created_at' => $now->copy()->subDays(2),
            ],
        ] as $transaction) {
            $product = $ppobProducts[$transaction['product']];

            PPOBTransaction::updateOrCreate([
                'external_reference' => $transaction['ref'],
            ], [
                'provider_id' => $provider->id,
                'product_id' => $product->id,
                'branch_id' => $branches[$transaction['branch']]->id,
                'created_by' => $users[$transaction['cashier']]->id,
                'amount' => $product->price,
                'fee' => $product->price - $product->cost,
                'status' => $transaction['status'],
                'response' => ['seed' => true],
                'created_at' => $transaction['created_at'],
                'updated_at' => $transaction['created_at'],
            ]);
        }
    }
}
