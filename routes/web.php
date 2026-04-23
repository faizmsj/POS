<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\PPOBProductController;
use App\Http\Controllers\PPOBProviderController;
use App\Http\Controllers\PPOBTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('branches', BranchController::class)->except(['show']);
Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('products', ProductController::class)->except(['show']);
Route::resource('suppliers', SupplierController::class)->except(['show']);
Route::resource('customers', CustomerController::class)->except(['show']);

Route::resource('purchases', PurchaseController::class)->only(['index', 'store']);
Route::resource('sales', SaleController::class)->only(['index', 'create', 'store']);
Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
Route::get('labels', [LabelController::class, 'index'])->name('labels.index');

Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
Route::get('settings/receipt-preview', [SettingController::class, 'receiptPreview'])->name('settings.receipt-preview');

Route::prefix('ppob')->name('ppob.')->group(function () {
    Route::resource('providers', PPOBProviderController::class)->names('providers')->only(['index', 'store', 'destroy']);
    Route::resource('products', PPOBProductController::class)->names('products')->only(['index', 'store', 'destroy']);
    Route::resource('transactions', PPOBTransactionController::class)->names('transactions')->only(['index', 'store']);
});
