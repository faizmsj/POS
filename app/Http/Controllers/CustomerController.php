<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'code' => 'required|string|max:100|unique:customers,code',
        ]);

        Customer::create($request->only(['name', 'email', 'phone', 'code', 'loyalty_tier']));

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'code' => 'required|string|max:100|unique:customers,code,' . $customer->id,
        ]);

        $customer->update($request->only(['name', 'email', 'phone', 'code', 'loyalty_tier']));

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
