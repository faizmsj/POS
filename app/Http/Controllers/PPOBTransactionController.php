<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\PPOBProduct;
use App\Models\PPOBTransaction;
use App\Services\PPOBService;
use Illuminate\Http\Request;

class PPOBTransactionController extends Controller
{
    public function index()
    {
        $transactions = PPOBTransaction::with(['provider', 'product', 'branch'])->orderByDesc('created_at')->get();

        return view('ppob.transactions.index', [
            'transactions' => $transactions,
            'branches' => Branch::orderBy('name')->get(),
            'products' => PPOBProduct::with('provider')->where('is_active', true)->get(),
            'transactionSummary' => [
                'count' => $transactions->count(),
                'success' => $transactions->where('status', 'completed')->count() + $transactions->where('status', 'success')->count(),
                'pending' => $transactions->whereNotIn('status', ['completed', 'success'])->count(),
                'amount' => (float) $transactions->sum('amount'),
            ],
        ]);
    }

    public function store(Request $request, PPOBService $service)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:ppob_products,id',
        ]);

        $product = PPOBProduct::findOrFail($request->product_id);
        $transaction = $service->createTransaction($product, $request->branch_id, 1, ['requested_at' => now()->toDateTimeString()]);
        $service->completeTransaction($transaction, ['status' => 'completed', 'metadata' => ['issued_by' => 'system']]);

        return redirect()->route('ppob.transactions.index')->with('success', 'Transaksi PPOB berhasil dibuat.');
    }
}
