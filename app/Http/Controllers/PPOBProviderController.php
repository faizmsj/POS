<?php

namespace App\Http\Controllers;

use App\Models\PPOBProvider;
use Illuminate\Http\Request;

class PPOBProviderController extends Controller
{
    public function index()
    {
        return view('ppob.providers.index', [
            'providers' => PPOBProvider::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:ppob_providers,code',
            'api_endpoint' => 'nullable|url|max:500',
        ]);

        PPOBProvider::create([
            'name' => $request->name,
            'code' => $request->code,
            'api_endpoint' => $request->api_endpoint,
            'credentials' => ['api_key' => $request->api_key],
        ]);

        return redirect()->route('ppob.providers.index')->with('success', 'Provider PPOB berhasil ditambahkan.');
    }

    public function destroy(PPOBProvider $provider)
    {
        $provider->delete();

        return redirect()->route('ppob.providers.index')->with('success', 'Provider PPOB berhasil dihapus.');
    }
}
