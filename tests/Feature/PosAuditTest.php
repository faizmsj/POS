<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PosAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
    }

    public function test_login_redirects_to_dashboard_for_valid_user(): void
    {
        $branch = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_can_register_cashier_account(): void
    {
        $branch = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);

        $response = $this->post('/register', [
            'name' => 'Kasir Baru',
            'email' => 'kasirbaru@example.com',
            'branch_id' => $branch->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'kasirbaru@example.com',
            'role' => 'cashier',
            'branch_id' => $branch->id,
        ]);
    }

    public function test_dashboard_can_filter_by_branch_and_render_sales_trend(): void
    {
        $branchA = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);
        $branchB = Branch::create([
            'name' => 'Kasir Selatan',
            'code' => 'KSP002',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'branch_id' => $branchA->id,
        ]);

        Sale::create([
            'invoice' => 'PST/20260425/0001',
            'branch_id' => $branchA->id,
            'subtotal' => 120000,
            'discount' => 0,
            'tax' => 0,
            'total' => 120000,
            'paid_amount' => 120000,
            'status' => 'completed',
            'created_by' => $admin->id,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Sale::create([
            'invoice' => 'PST/20260425/0002',
            'branch_id' => $branchB->id,
            'subtotal' => 45000,
            'discount' => 0,
            'tax' => 0,
            'total' => 45000,
            'paid_amount' => 45000,
            'status' => 'completed',
            'created_by' => $admin->id,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($admin)->get('/?branch_id='.$branchA->id.'&period=7days');

        $response->assertOk();
        $response->assertSee('Grafik tren penjualan 7 hari terakhir');
        $response->assertSee('sales-trend-chart', false);
        $response->assertSee((string) $branchA->name);
    }

    public function test_cashier_checkout_requires_open_shift_and_valid_paid_amount(): void
    {
        $branch = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);

        $cashier = User::create([
            'name' => 'Kasir Demo',
            'email' => 'cashier@example.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'branch_id' => $branch->id,
        ]);

        $product = Product::create([
            'name' => 'Produk Demo',
            'sku' => 'SKU-001',
            'barcode' => '899000000001',
            'cost_price' => 5000,
            'base_price' => 10000,
            'is_active' => true,
        ]);

        ProductBranch::create([
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'stock' => 10,
            'selling_price' => 10000,
            'margin_percent' => 50,
            'is_active' => true,
        ]);

        Setting::create([
            'key' => 'invoice_prefix',
            'value' => 'AUD',
            'group' => 'POS',
        ]);

        $payload = [
            'branch_id' => $branch->id,
            'discount' => 0,
            'tax' => 0,
            'paid_amount' => 9000,
            'items_json' => json_encode([
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => 10000,
                    'discount' => 0,
                    'total' => 10000,
                ],
            ]),
        ];

        $responseWithoutShift = $this->actingAs($cashier)->post('/sales', $payload);
        $responseWithoutShift->assertSessionHasErrors('items_json');

        CashierShift::create([
            'user_id' => $cashier->id,
            'branch_id' => $branch->id,
            'started_at' => now(),
            'opening_balance' => 100000,
            'cash_in' => 0,
            'cash_out' => 0,
            'status' => 'open',
        ]);

        $responseLowPayment = $this->actingAs($cashier)->post('/sales', $payload);
        $responseLowPayment->assertSessionHasErrors('items_json');

        $responseSuccess = $this->actingAs($cashier)->post('/sales', array_merge($payload, [
            'paid_amount' => 10000,
        ]));

        $responseSuccess->assertRedirect(route('sales.index'));
        $this->assertDatabaseHas('sales', [
            'branch_id' => $branch->id,
            'created_by' => $cashier->id,
            'invoice' => 'AUD/'.now()->format('Ymd').'/0001',
        ]);
    }

    public function test_purchase_records_creator_for_authenticated_user(): void
    {
        $branch = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);

        $manager = User::create([
            'name' => 'Manager Demo',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'branch_id' => $branch->id,
        ]);

        $product = Product::create([
            'name' => 'Produk Beli',
            'sku' => 'SKU-BELI',
            'barcode' => '899000000111',
            'cost_price' => 5000,
            'base_price' => 7000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($manager)->post('/purchases', [
            'branch_id' => $branch->id,
            'purchase_date' => now()->toDateString(),
            'product_id' => $product->id,
            'quantity' => 4,
            'unit_cost' => 5000,
            'tax' => 1000,
        ]);

        $response->assertRedirect(route('purchases.index'));

        $purchase = Purchase::first();
        $this->assertNotNull($purchase);
        $this->assertSame($manager->id, $purchase->created_by);
    }

    public function test_label_preview_only_lists_products_for_selected_branch(): void
    {
        $branchA = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);
        $branchB = Branch::create([
            'name' => 'Kasir Selatan',
            'code' => 'KSP002',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Demo',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => $branchA->id,
        ]);

        $productA = Product::create([
            'name' => 'Produk Cabang A',
            'sku' => 'SKU-A',
            'barcode' => '1111111111111',
            'cost_price' => 5000,
            'base_price' => 9000,
            'is_active' => true,
        ]);
        $productB = Product::create([
            'name' => 'Produk Cabang B',
            'sku' => 'SKU-B',
            'barcode' => '2222222222222',
            'cost_price' => 5000,
            'base_price' => 10000,
            'is_active' => true,
        ]);

        ProductBranch::create([
            'product_id' => $productA->id,
            'branch_id' => $branchA->id,
            'stock' => 5,
            'selling_price' => 9000,
            'margin_percent' => 44,
            'is_active' => true,
        ]);
        ProductBranch::create([
            'product_id' => $productB->id,
            'branch_id' => $branchB->id,
            'stock' => 5,
            'selling_price' => 10000,
            'margin_percent' => 50,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/labels?branch_id='.$branchA->id);

        $response->assertOk();
        $response->assertSee('Produk Cabang A');
        $response->assertDontSee('Produk Cabang B');
    }

    public function test_admin_cannot_create_owner_or_delete_self(): void
    {
        $branch = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $responseCreateOwner = $this->actingAs($admin)->post('/users', [
            'name' => 'Owner Baru',
            'email' => 'owner-baru@example.com',
            'role' => 'owner',
            'branch_id' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $responseCreateOwner->assertSessionHasErrors('role');

        $responseDeleteSelf = $this->actingAs($admin)->delete('/users/'.$admin->id);

        $responseDeleteSelf->assertRedirect(route('users.index'));
        $responseDeleteSelf->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => 'admin@example.com',
        ]);
    }

    public function test_manager_and_cashier_role_require_branch_assignment(): void
    {
        $branch = Branch::create([
            'name' => 'Kasir Pusat',
            'code' => 'KSP001',
            'is_active' => true,
        ]);

        $owner = User::create([
            'name' => 'Owner Demo',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($owner)->post('/users', [
            'name' => 'Kasir Tanpa Cabang',
            'email' => 'kasir-tanpa-cabang@example.com',
            'role' => 'cashier',
            'branch_id' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('branch_id');
    }
}
