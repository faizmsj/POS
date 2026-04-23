<?php

namespace App\Http\Controllers;

use App\Models\PPOBProduct;
use App\Models\PPOBProvider;
use Illuminate\Http\Request;

class PPOBProductController extends Controller
{
    public function index()
    {
        $products = PPOBProduct::with('provider')->orderBy('name')->get();

        return view('ppob.products.index', [
            'products' => $products,
            'providers' => PPOBProvider::orderBy('name')->get(),
            'productCategories' => $products->pluck('category')->filter()->unique()->values(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:ppob_providers,id',
            'code' => 'required|string|max:100|unique:ppob_products,code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        PPOBProduct::create([
            'provider_id' => $request->provider_id,
            'code' => $request->code,
            'name' => $request->name,
            'category' => $request->category,
            'cost' => $request->cost,
            'price' => $request->price,
            'margin_percent' => $request->price > 0 ? round((($request->price - $request->cost) / $request->price) * 100, 2) : 0,
            'metadata' => [],
        ]);

        return redirect()->route('ppob.products.index')->with('success', 'Produk PPOB berhasil ditambahkan.');
    }

    public function destroy(PPOBProduct $product)
    {
        $product->delete();

        return redirect()->route('ppob.products.index')->with('success', 'Produk PPOB berhasil dihapus.');
    }
}
