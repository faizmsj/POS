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
        $accessibleBranchIds = $this->accessibleBranchIds();
        $branchId = $request->integer('branch_id');
        $selectedBranchId = $branchId && $accessibleBranchIds->contains($branchId) ? $branchId : null;
        $branchIds = $selectedBranchId ? collect([$selectedBranchId]) : $accessibleBranchIds;
        $period = $request->string('period')->toString() ?: 'month';
        $periodStartInput = $request->string('start_date')->toString();
        $periodEndInput = $request->string('end_date')->toString();

        [$periodStart, $periodEnd, $periodLabel, $periodKey] = $this->resolvePeriod(
            $period,
            $periodStartInput,
            $periodEndInput
        );

        $grossSalesToday = (float) $this->scopeToAccessibleBranches(
            Sale::query()->whereBetween('created_at', [$periodStart, $periodEnd])
        )->sum('total');
        $transactionCountToday = $this->scopeToAccessibleBranches(
            Sale::query()->whereBetween('created_at', [$periodStart, $periodEnd])
        )->count();
        $ppobSalesToday = (float) $this->scopeToAccessibleBranches(
            PPOBTransaction::query()->whereBetween('created_at', [$periodStart, $periodEnd])
        )->sum('amount');
        $pendingPpobToday = $this->scopeToAccessibleBranches(
            PPOBTransaction::query()->whereBetween('created_at', [$periodStart, $periodEnd])
        )
            ->where('status', '!=', 'success')
            ->count();
        $loyaltyPointsIssued = (float) CustomerPointHistory::where('type', 'earn')
            ->whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->sum('points');
        $openShifts = CashierShift::where('status', 'open')->whereIn('branch_id', $branchIds)->count();
        $activeBranches = Branch::where('is_active', true)->whereIn('id', $branchIds)->count();
        $activeProducts = Product::whereIn('id', ProductBranch::whereIn('branch_id', $branchIds)->pluck('product_id')->unique())->where('is_active', true)->count();
        $lowStockItems = ProductBranch::where('is_active', true)
            ->whereIn('branch_id', $branchIds)
            ->where('stock', '<=', 10)
            ->count();
        $stockValue = (float) PurchaseBatch::whereIn('branch_id', $branchIds)->sum('total_cost');

        $cogsToday = (float) SaleItem::query()
            ->join('purchase_batches', 'sale_items.purchase_batch_id', '=', 'purchase_batches.id')
            ->whereIn('sale_items.branch_id', $branchIds)
            ->whereBetween('sale_items.created_at', [$periodStart, $periodEnd])
            ->sum(DB::raw('sale_items.quantity * purchase_batches.unit_cost'));

        $profitToday = $grossSalesToday - $cogsToday;
        $marginToday = $grossSalesToday > 0 ? ($profitToday / $grossSalesToday) * 100 : 0;
        $averageTransaction = $transactionCountToday > 0 ? $grossSalesToday / $transactionCountToday : 0;

        $branchPerformance = Branch::query()
            ->whereIn('id', $branchIds)
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
            ->whereIn('sale_items.branch_id', $branchIds)
            ->whereBetween('sale_items.created_at', [$periodStart, $periodEnd])
            ->with('product')
            ->groupBy('sale_items.product_id')
            ->orderByDesc('quantity_sold')
            ->take(5)
            ->get();

        $recentSales = Sale::query()
            ->with(['branch', 'customer'])
            ->whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->latest()
            ->take(6)
            ->get();

        $recentPpobTransactions = PPOBTransaction::query()
            ->with(['product', 'provider', 'branch'])
            ->whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->latest()
            ->take(4)
            ->get();

        $stockAlerts = ProductBranch::query()
            ->with(['product', 'branch'])
            ->where('is_active', true)
            ->whereIn('branch_id', $branchIds)
            ->orderBy('stock')
            ->take(5)
            ->get();

        $salesTrend = $this->buildSalesTrend($branchIds->all());

        return view('dashboard', [
            'branchOptions' => $this->accessibleBranches()->get(),
            'selectedBranchId' => $selectedBranchId,
            'branches' => Branch::whereIn('id', $branchIds)->count(),
            'products' => Product::whereIn('id', ProductBranch::whereIn('branch_id', $branchIds)->pluck('product_id')->unique())->count(),
            'customers' => Customer::count(),
            'sales' => Sale::whereIn('branch_id', $branchIds)->count(),
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
            'salesTrend' => $salesTrend['points'],
            'salesTrendPath' => $salesTrend['path'],
            'salesTrendAreaPath' => $salesTrend['area_path'],
            'periodLabel' => $periodLabel,
            'periodKey' => $periodKey,
            'startDate' => $periodStart->toDateString(),
            'endDate' => $periodEnd->toDateString(),
            'todayLabel' => $today->translatedFormat('l, d F Y'),
        ]);
    }

    private function resolvePeriod(string $period, string $startDate = '', string $endDate = ''): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today' => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                'Hari Ini',
                'today',
            ],
            '7days' => [
                $now->copy()->subDays(6)->startOfDay(),
                $now->copy()->endOfDay(),
                '7 Hari Terakhir',
                '7days',
            ],
            '30days' => [
                $now->copy()->subDays(29)->startOfDay(),
                $now->copy()->endOfDay(),
                '30 Hari Terakhir',
                '30days',
            ],
            'custom' => $this->resolveCustomPeriod($startDate, $endDate),
            default => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfDay(),
                'Bulan Ini',
                'month',
            ],
        };
    }

    private function resolveCustomPeriod(string $startDate = '', string $endDate = ''): array
    {
        $fallbackStart = Carbon::now()->startOfMonth();
        $fallbackEnd = Carbon::now()->endOfDay();

        try {
            $start = $startDate !== ''
                ? Carbon::parse($startDate)->startOfDay()
                : $fallbackStart->copy();
            $end = $endDate !== ''
                ? Carbon::parse($endDate)->endOfDay()
                : $fallbackEnd->copy();
        } catch (\Throwable $exception) {
            $start = $fallbackStart->copy();
            $end = $fallbackEnd->copy();
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [
            $start,
            $end,
            $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y'),
            'custom',
        ];
    }

    private function buildSalesTrend(array $branchIds): array
    {
        $start = Carbon::now()->subDays(6)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $salesByDate = Sale::query()
            ->selectRaw('DATE(created_at) as sale_date, SUM(total) as total_sales, COUNT(*) as total_transactions')
            ->whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        $points = collect(range(0, 6))->map(function (int $offset) use ($start, $salesByDate) {
            $date = $start->copy()->addDays($offset);
            $row = $salesByDate->get($date->toDateString());

            return [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('d M'),
                'day' => $date->translatedFormat('D'),
                'sales' => (float) ($row->total_sales ?? 0),
                'transactions' => (int) ($row->total_transactions ?? 0),
            ];
        });

        return [
            'points' => $points,
            'path' => $this->buildSmoothPath($points),
            'area_path' => $this->buildSmoothAreaPath($points),
        ];
    }

    private function buildSmoothPath($points): string
    {
        $coordinates = $this->trendCoordinates($points);

        if ($coordinates->isEmpty()) {
            return '';
        }

        if ($coordinates->count() === 1) {
            $point = $coordinates->first();

            return "M {$point['x']} {$point['y']}";
        }

        $path = "M {$coordinates[0]['x']} {$coordinates[0]['y']}";

        for ($i = 0; $i < $coordinates->count() - 1; $i++) {
            $current = $coordinates[$i];
            $next = $coordinates[$i + 1];
            $controlX = round(($current['x'] + $next['x']) / 2, 2);

            $path .= " C {$controlX} {$current['y']}, {$controlX} {$next['y']}, {$next['x']} {$next['y']}";
        }

        return $path;
    }

    private function buildSmoothAreaPath($points): string
    {
        $coordinates = $this->trendCoordinates($points);

        if ($coordinates->isEmpty()) {
            return '';
        }

        $linePath = $this->buildSmoothPath($points);
        $first = $coordinates->first();
        $last = $coordinates->last();

        return "{$linePath} L {$last['x']} 172 L {$first['x']} 172 Z";
    }

    private function trendCoordinates($points)
    {
        $trendMax = max(1, (float) $points->max('sales'));
        $count = max(1, $points->count() - 1);

        return $points->values()->map(function (array $point, int $index) use ($count, $trendMax) {
            $x = round(28 + (($index / $count) * 564), 2);
            $y = round(172 - (($point['sales'] / $trendMax) * 136), 2);

            return [
                'x' => $x,
                'y' => $y,
            ];
        });
    }
}
