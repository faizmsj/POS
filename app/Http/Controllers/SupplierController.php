<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private const PHONE_RULE = ['nullable', 'string', 'regex:/^[0-9+\-\s()]{8,20}$/'];

    public function index(?Supplier $editing = null)
    {
        return view('suppliers.index', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'editing' => $editing,
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return $this->index($supplier);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => self::PHONE_RULE,
            'address' => 'nullable|string|max:500',
        ]);

        $validated['email'] = filled($validated['email'] ?? null) ? strtolower((string) $validated['email']) : null;

        Supplier::create(array_merge(
            $request->only(['name', 'address', 'notes']),
            [
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]
        ));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => self::PHONE_RULE,
            'address' => 'nullable|string|max:500',
        ]);

        $validated['email'] = filled($validated['email'] ?? null) ? strtolower((string) $validated['email']) : null;

        $supplier->update(array_merge(
            $request->only(['name', 'address', 'notes']),
            [
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]
        ));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
