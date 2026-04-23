<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseBatch;
use App\Models\PPOBProvider;
use App\Models\PPOBProduct;
use App\Models\PPOBTransaction;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branch = Branch::updateOrCreate([
            'code' => 'KSP001',
        ], [
            'name' => 'Kasir Pusat',
            'address' => 'Jalan Pahlawan No. 1',
            'phone' => '+6281234567890',
            'configuration' => ['timezone' => 'Asia/Jakarta'],
            'is_active' => true,
        ]);

        $branchSecondary = Branch::updateOrCreate([
            'code' => 'KSP002',
        ], [
            'name' => 'Kasir Selatan',
            'address' => 'Jalan Melati No. 22',
            'phone' => '+628123450000',
            'configuration' => ['timezone' => 'Asia/Jakarta'],
            'is_active' => true,
        ]);

        $user = User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrator Pusat',
            'branch_id' => $branch->id,
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $cashier = User::updateOrCreate([
            'email' => 'cashier@example.com',
        ], [
            'name' => 'Kasir Pusat',
            'branch_id' => $branch->id,
            'role' => 'cashier',
            'password' => bcrypt('password'),
        ]);

        $supplier = Supplier::firstOrCreate([
            'email' => 'supplier@example.com',
        ], [
            'name' => 'Supplier Utama',
            'phone' => '+6280987654321',
            'address' => 'Gudang Utama',
        ]);

        $supplierTwo = Supplier::firstOrCreate([
            'email' => 'frozen@example.com',
        ], [
            'name' => 'Frozen Food Nusantara',
            'phone' => '+628088888888',
            'address' => 'Distribusi Kota',
        ]);

        $categories = collect();

        if (Schema::hasTable('categories')) {
            $categories = collect([
                ['name' => 'Makanan', 'slug' => 'makanan'],
                ['name' => 'Minuman', 'slug' => 'minuman'],
                ['name' => 'Sembako', 'slug' => 'sembako'],
            ])->mapWithKeys(function (array $category) {
                $model = Category::updateOrCreate(['slug' => $category['slug']], [
                    'name' => $category['name'],
                    'is_active' => true,
                ]);

                return [$category['slug'] => $model];
            });
        }

        $products = collect([
            ['sku' => 'FOOD-001', 'name' => 'Nasi Goreng Spesial', 'barcode' => '1234567890123', 'cost_price' => 12000, 'base_price' => 21000, 'category_slug' => 'makanan'],
            ['sku' => 'FOOD-002', 'name' => 'Mie Goreng', 'barcode' => '1234567890124', 'cost_price' => 4500, 'base_price' => 7500, 'category_slug' => 'makanan'],
            ['sku' => 'DRINK-001', 'name' => 'Es Teh Manis', 'barcode' => '1234567890125', 'cost_price' => 4000, 'base_price' => 8000, 'category_slug' => 'minuman'],
            ['sku' => 'DRINK-002', 'name' => 'Air Mineral 600ml', 'barcode' => '1234567890126', 'cost_price' => 3500, 'base_price' => 6000, 'category_slug' => 'minuman'],
            ['sku' => 'BASIC-001', 'name' => 'Beras 5kg', 'barcode' => '1234567890127', 'cost_price' => 62000, 'base_price' => 69000, 'category_slug' => 'sembako'],
        ])->map(function (array $productData) use ($categories) {
            return Product::updateOrCreate([
                'barcode' => $productData['barcode'],
            ], [
                'sku' => $productData['sku'],
                'name' => $productData['name'],
                'barcode' => $productData['barcode'],
                'category_id' => $categories->get($productData['category_slug'])?->id,
                'cost_price' => $productData['cost_price'],
                'base_price' => $productData['base_price'],
                'is_active' => true,
            ]);
        });

        foreach ($products as $index => $product) {
            foreach ([$branch, $branchSecondary] as $branchItem) {
                $price = (float) $product->base_price + ($branchItem->id === $branchSecondary->id ? 500 : 0);
                ProductBranch::updateOrCreate([
                    'product_id' => $product->id,
                    'branch_id' => $branchItem->id,
                ], [
                    'stock' => 20 + ($index * 8),
                    'selling_price' => $price,
                    'margin_percent' => $price > 0 ? round((($price - (float) $product->cost_price) / $price) * 100, 2) : 0,
                    'is_active' => true,
                ]);
            }
        }

        Customer::firstOrCreate([
            'code' => 'CUS-001',
        ], [
            'name' => 'Pelanggan Biasa',
            'email' => 'customer@example.com',
            'phone' => '+628111222333',
            'points_balance' => 0,
        ]);

        Customer::firstOrCreate([
            'code' => 'CUS-002',
        ], [
            'name' => 'Member Gold',
            'email' => 'gold@example.com',
            'phone' => '+628111222334',
            'points_balance' => 120,
            'loyalty_tier' => 'gold',
        ]);

        $provider = PPOBProvider::firstOrCreate([
            'code' => 'PPOB-A',
        ], [
            'name' => 'Provider PPOB',
            'api_endpoint' => 'https://api.example.com',
            'credentials' => ['api_key' => 'secret'],
            'is_enabled' => true,
        ]);

        $ppobProductOne = PPOBProduct::updateOrCreate([
            'code' => 'PULSA-10K',
        ], [
            'provider_id' => $provider->id,
            'name' => 'Pulsa 10K',
            'category' => 'Pulsa',
            'cost' => 9000,
            'price' => 10000,
            'margin_percent' => 11.11,
            'is_active' => true,
        ]);

        $ppobProductTwo = PPOBProduct::updateOrCreate([
            'code' => 'DATA-5GB',
        ], [
            'provider_id' => $provider->id,
            'name' => 'Paket Data 5GB',
            'category' => 'Data',
            'cost' => 23000,
            'price' => 25000,
            'margin_percent' => 8,
            'is_active' => true,
        ]);

        foreach ([
            ['key' => 'store_name', 'value' => 'Kasir Pusat Store', 'group' => 'Umum'],
            ['key' => 'invoice_prefix', 'value' => 'PST', 'group' => 'POS'],
            ['key' => 'loyalty_enabled', 'value' => '1', 'group' => 'Loyalty'],
            ['key' => 'points_per_amount', 'value' => '1000', 'group' => 'Loyalty'],
        ] as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        $purchase = Purchase::updateOrCreate([
            'reference' => 'PUR/DEMO/0001',
        ], [
            'supplier_id' => $supplier->id,
            'branch_id' => $branch->id,
            'purchase_date' => now()->subDays(3)->toDateString(),
            'subtotal' => 240000,
            'tax' => 0,
            'total' => 240000,
            'status' => 'received',
            'notes' => 'Seed pembelian demo',
        ]);

        PurchaseBatch::updateOrCreate([
            'purchase_id' => $purchase->id,
            'product_id' => $products[0]->id,
            'branch_id' => $branch->id,
        ], [
            'quantity' => 20,
            'unit_cost' => 12000,
            'total_cost' => 240000,
            'received_at' => now()->subDays(3)->toDateString(),
        ]);

        $sale = Sale::updateOrCreate([
            'invoice' => 'PST/DEMO/0001',
        ], [
            'branch_id' => $branch->id,
            'customer_id' => Customer::where('code', 'CUS-001')->value('id'),
            'created_by' => $cashier->id,
            'subtotal' => 29000,
            'discount' => 1000,
            'tax' => 0,
            'total' => 28000,
            'paid_amount' => 30000,
            'status' => 'completed',
            'notes' => 'Seed penjualan demo',
        ]);

        CashierShift::updateOrCreate([
            'user_id' => $cashier->id,
            'branch_id' => $branch->id,
            'started_at' => now()->copy()->startOfDay()->addHours(8),
        ], [
            'opening_balance' => 250000,
            'closing_balance' => null,
            'cash_in' => 30000,
            'cash_out' => 5000,
            'ended_at' => null,
            'status' => 'open',
        ]);

        CashierShift::updateOrCreate([
            'user_id' => $user->id,
            'branch_id' => $branchSecondary->id,
            'started_at' => now()->copy()->subDay()->startOfDay()->addHours(9),
        ], [
            'opening_balance' => 300000,
            'closing_balance' => 415000,
            'cash_in' => 45000,
            'cash_out' => 10000,
            'ended_at' => now()->copy()->subDay()->startOfDay()->addHours(18),
            'status' => 'closed',
        ]);

        SaleItem::updateOrCreate([
            'sale_id' => $sale->id,
            'product_id' => $products[0]->id,
            'branch_id' => $branch->id,
        ], [
            'quantity' => 1,
            'unit_price' => 21000,
            'discount' => 1000,
            'tax' => 0,
            'total' => 20000,
        ]);

        SaleItem::updateOrCreate([
            'sale_id' => $sale->id,
            'product_id' => $products[2]->id,
            'branch_id' => $branch->id,
        ], [
            'quantity' => 1,
            'unit_price' => 8000,
            'discount' => 0,
            'tax' => 0,
            'total' => 8000,
        ]);

        PPOBTransaction::updateOrCreate([
            'external_reference' => 'PPOB-DEMO-0001',
        ], [
            'provider_id' => $provider->id,
            'product_id' => $ppobProductOne->id,
            'branch_id' => $branch->id,
            'created_by' => $cashier->id,
            'amount' => $ppobProductOne->price,
            'fee' => $ppobProductOne->price - $ppobProductOne->cost,
            'status' => 'completed',
            'response' => ['seed' => true],
        ]);

        PPOBTransaction::updateOrCreate([
            'external_reference' => 'PPOB-DEMO-0002',
        ], [
            'provider_id' => $provider->id,
            'product_id' => $ppobProductTwo->id,
            'branch_id' => $branchSecondary->id,
            'created_by' => $cashier->id,
            'amount' => $ppobProductTwo->price,
            'fee' => $ppobProductTwo->price - $ppobProductTwo->cost,
            'status' => 'pending',
            'response' => ['seed' => true],
        ]);
    }
}
