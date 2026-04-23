<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\User;
use Illuminate\Http\Request;

class CashierShiftController extends Controller
{
    public function index()
    {
        $branchIds = $this->accessibleBranchIds();
        $shifts = CashierShift::with(['user', 'branch'])
            ->withSum('sales', 'total')
            ->whereIn('branch_id', $branchIds)
            ->when(auth()->user()->hasRole('cashier'), fn ($query) => $query->where('user_id', auth()->id()))
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->latest('started_at')
            ->get();

        return view('cashier-shifts.index', [
            'shifts' => $shifts,
            'branches' => $this->accessibleBranches()->get(),
            'cashiers' => auth()->user()->hasRole('cashier')
                ? User::whereKey(auth()->id())->get()
                : User::where('role', 'cashier')->whereIn('branch_id', $branchIds)->orderBy('name')->get(),
            'openCount' => $shifts->where('status', 'open')->count(),
            'closedTodayCount' => $shifts->where('status', 'closed')->filter(fn ($shift) => $shift->ended_at && $shift->ended_at->isToday())->count(),
            'openBalanceTotal' => (float) $shifts->where('status', 'open')->sum('opening_balance'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'opening_balance' => 'required|numeric|min:0',
            'cash_in' => 'nullable|numeric|min:0',
            'cash_out' => 'nullable|numeric|min:0',
        ]);

        $this->ensureBranchAccess((int) $validated['branch_id']);

        if (auth()->user()->hasRole('cashier')) {
            $validated['user_id'] = auth()->id();
            $validated['branch_id'] = auth()->user()->branch_id;
        }

        $hasOpenShift = CashierShift::where('user_id', $validated['user_id'])
            ->where('status', 'open')
            ->exists();

        if ($hasOpenShift) {
            return redirect()->route('cashier-shifts.index')->with('error', 'Kasir tersebut masih memiliki shift yang belum ditutup.');
        }

        CashierShift::create([
            'user_id' => $validated['user_id'],
            'branch_id' => $validated['branch_id'],
            'started_at' => now(),
            'opening_balance' => $validated['opening_balance'],
            'cash_in' => $validated['cash_in'] ?? 0,
            'cash_out' => $validated['cash_out'] ?? 0,
            'status' => 'open',
        ]);

        return redirect()->route('cashier-shifts.index')->with('success', 'Shift kasir berhasil dibuka.');
    }

    public function update(Request $request, CashierShift $cashier_shift)
    {
        $this->ensureBranchAccess((int) $cashier_shift->branch_id);

        if (auth()->user()->hasRole('cashier') && (int) $cashier_shift->user_id !== (int) auth()->id()) {
            abort(403, 'Anda tidak dapat mengubah shift kasir lain.');
        }

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'cash_in' => 'nullable|numeric|min:0',
            'cash_out' => 'nullable|numeric|min:0',
        ]);

        if ($cashier_shift->status !== 'open') {
            return redirect()->route('cashier-shifts.index')->with('error', 'Shift ini sudah ditutup.');
        }

        $cashier_shift->update([
            'closing_balance' => $validated['closing_balance'],
            'cash_in' => $validated['cash_in'] ?? $cashier_shift->cash_in,
            'cash_out' => $validated['cash_out'] ?? $cashier_shift->cash_out,
            'ended_at' => now(),
            'status' => 'closed',
        ]);

        return redirect()->route('cashier-shifts.index')->with('success', 'Shift kasir berhasil ditutup.');
    }
}
