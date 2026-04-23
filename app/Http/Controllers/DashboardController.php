<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\CustomerPointHistory;
use App\Models\PPOBTransaction;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\PurchaseBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $periodStart = Carbon::now()->startOfMonth();
        $periodEnd = Carbon::now()->endOfDay();

        $grossSalesToday = (float) Sale::whereDate('created_at', $today)->sum('total');
        $transactionCountToday = Sale::whereDate('created_at', $today)->count();
        $ppobSalesToday = (float) PPOBTransaction::whereDate('created_at', $today)->sum('amount');
        $pendingPpobToday = PPOBTransaction::whereDate('created_at', $today)
            ->where('status', '!=', 'success')
            ->count();
        $loyaltyPointsIssued = (float) CustomerPointHistory::where('type', 'earn')->sum('points');
        $openShifts = CashierShift::where('status', 'open')->count();
        $activeBranches = Branch::where('is_active', true)->count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockItems = ProductBranch::where('is_active', true)
            ->where('stock', '<=', 10)
            ->count();
        $stockValue = (float) PurchaseBatch::sum('total_cost');

        $cogsToday = (float) SaleItem::query()
            ->join('purchase_batches', 'sale_items.purchase_batch_id', '=', 'purchase_batches.id')
            ->whereDate('sale_items.created_at', $today)
            ->sum(DB::raw('sale_items.quantity * purchase_batches.unit_cost'));

        $profitToday = $grossSalesToday - $cogsToday;
        $marginToday = $grossSalesToday > 0 ? ($profitToday / $grossSalesToday) * 100 : 0;
        $averageTransaction = $transactionCountToday > 0 ? $grossSalesToday / $transactionCountToday : 0;

        $branchPerformance = Branch::query()
            ->withSum([
                'sales as period_total' => fn ($query) => $query->whereBetween('created_at', [$periodStart, $periodEnd]),
            ], 'total')
            ->withCount([
                'sales as period_transactions' => fn ($query) => $query->whereBetween('created_at', [$periodStart, $periodEnd]),
            ])
            ->orderByDesc('period_total')
            ->take(4)
            ->get();

        $topProducts = SaleItem::query()
            ->select('sale_items.product_id')
            ->selectRaw('SUM(sale_items.quantity) as quantity_sold')
            ->selectRaw('SUM(sale_items.total) as revenue')
            ->selectRaw('SUM(sale_items.quantity * COALESCE(purchase_batches.unit_cost, 0)) as cogs')
            ->leftJoin('purchase_batches', 'sale_items.purchase_batch_id', '=', 'purchase_batches.id')
            ->with('product')
            ->groupBy('sale_items.product_id')
            ->orderByDesc('quantity_sold')
            ->take(5)
            ->get();

        $recentSales = Sale::query()
            ->with(['branch', 'customer'])
            ->latest()
            ->take(6)
            ->get();

        $recentPpobTransactions = PPOBTransaction::query()
            ->with(['product', 'provider', 'branch'])
            ->latest()
            ->take(4)
            ->get();

        $stockAlerts = ProductBranch::query()
            ->with(['product', 'branch'])
            ->where('is_active', true)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', [
            'branches' => Branch::count(),
            'products' => Product::count(),
            'customers' => Customer::count(),
            'sales' => Sale::count(),
            'grossSalesToday' => $grossSalesToday,
            'transactionCountToday' => $transactionCountToday,
            'ppobSalesToday' => $ppobSalesToday,
            'pendingPpobToday' => $pendingPpobToday,
            'profitToday' => $profitToday,
            'cogsToday' => $cogsToday,
            'marginToday' => $marginToday,
            'averageTransaction' => $averageTransaction,
            'loyaltyPointsIssued' => $loyaltyPointsIssued,
            'openShifts' => $openShifts,
            'activeBranches' => $activeBranches,
            'activeProducts' => $activeProducts,
            'lowStockItems' => $lowStockItems,
            'stockValue' => $stockValue,
            'branchPerformance' => $branchPerformance,
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'recentPpobTransactions' => $recentPpobTransactions,
            'stockAlerts' => $stockAlerts,
            'periodLabel' => $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y'),
            'todayLabel' => $today->translatedFormat('l, d F Y'),
        ]);
    }
}
