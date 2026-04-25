<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CashierShiftController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\PPOBProductController;
use App\Http\Controllers\PPOBProviderController;
use App\Http\Controllers\PPOBTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('role:owner,admin,manager,cashier');

    Route::middleware('role:owner,admin,manager')->group(function () {
        Route::resource('branches', BranchController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'store']);
        Route::get('labels', [LabelController::class, 'index'])->name('labels.index');
    });

    Route::middleware('role:owner,admin,manager,cashier')->group(function () {
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::resource('cashier-shifts', CashierShiftController::class)->only(['index', 'store', 'update']);
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store']);
        Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
        Route::get('help/faq', [HelpController::class, 'faq'])->name('help.faq');
        Route::get('help/sop', [HelpController::class, 'sop'])->name('help.sop');
        Route::prefix('ppob')->name('ppob.')->group(function () {
            Route::resource('transactions', PPOBTransactionController::class)->names('transactions')->only(['index', 'store']);
        });
    });

    Route::middleware('role:owner,admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'create']);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
        Route::get('settings/receipt-preview', [SettingController::class, 'receiptPreview'])->name('settings.receipt-preview');
        Route::prefix('ppob')->name('ppob.')->group(function () {
            Route::resource('providers', PPOBProviderController::class)->names('providers')->only(['index', 'store', 'destroy']);
            Route::resource('products', PPOBProductController::class)->names('products')->only(['index', 'store', 'destroy']);
        });
    });
});
