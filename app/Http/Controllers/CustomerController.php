<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private const PHONE_RULE = ['nullable', 'string', 'regex:/^[0-9+\-\s()]{8,20}$/'];

    public function index(?Customer $editing = null)
    {
        return view('customers.index', [
            'customers' => Customer::orderBy('name')->get(),
            'editing' => $editing,
        ]);
    }

    public function edit(Customer $customer)
    {
        return $this->index($customer);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => self::PHONE_RULE,
            'code' => 'required|string|max:100|unique:customers,code',
        ]);

        $validated['email'] = filled($validated['email'] ?? null) ? strtolower((string) $validated['email']) : null;

        Customer::create(array_merge(
            $request->only(['name', 'code', 'loyalty_tier']),
            [
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]
        ));

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => self::PHONE_RULE,
            'code' => 'required|string|max:100|unique:customers,code,' . $customer->id,
        ]);

        $validated['email'] = filled($validated['email'] ?? null) ? strtolower((string) $validated['email']) : null;

        $customer->update(array_merge(
            $request->only(['name', 'code', 'loyalty_tier']),
            [
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]
        ));

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
