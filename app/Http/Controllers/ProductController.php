<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(?Product $editing = null)
    {
        $hasCategories = Schema::hasTable('categories');
        $branchIds = $this->accessibleBranchIds();

        return view('products.index', [
            'products' => Product::with($hasCategories ? ['branches.branch', 'category'] : ['branches.branch'])
                ->whereHas('branches', fn ($query) => $query->whereIn('branch_id', $branchIds))
                ->orderBy('name')
                ->get(),
            'branches' => $this->accessibleBranches()->get(),
            'categories' => $hasCategories ? Category::orderBy('name')->get() : collect(),
            'categoryTableMissing' => ! $hasCategories,
            'editing' => $editing?->load($hasCategories ? ['branches', 'category'] : ['branches']),
        ]);
    }

    public function edit(Product $product)
    {
        $this->ensureProductAccess($product);

        return $this->index($product);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'image_url' => 'nullable|string|max:2048',
            'image' => 'nullable|image|max:3072',
            'category_id' => Schema::hasTable('categories') ? 'nullable|exists:categories,id' : 'nullable',
            'cost_price' => 'nullable|numeric|min:0',
            'base_price' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'stock' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        if ($request->filled('branch_id')) {
            $this->ensureBranchAccess((int) $request->branch_id);
        }

        $product = Product::create(array_merge(
            $request->only(['name', 'sku', 'barcode', 'category_id', 'cost_price', 'base_price', 'description']),
            [
                'meta' => [
                    'image_url' => $this->storeProductImage($request) ?? $request->input('image_url'),
                ],
            ]
        ));

        if ($request->filled('branch_id')) {
            ProductBranch::create([
                'product_id' => $product->id,
                'branch_id' => $request->branch_id,
                'stock' => $request->stock ?: 0,
                'selling_price' => $request->selling_price ?: $product->base_price,
                'margin_percent' => $product->base_price > 0 ? round((($request->selling_price ?: $product->base_price) - $product->cost_price) / $product->base_price * 100, 2) : 0,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $this->ensureProductAccess($product);
        $hasCategories = Schema::hasTable('categories');

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'image_url' => 'nullable|string|max:2048',
            'image' => 'nullable|image|max:3072',
            'category_id' => $hasCategories ? 'nullable|exists:categories,id' : 'nullable',
            'cost_price' => 'nullable|numeric|min:0',
            'base_price' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'stock' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        if ($request->filled('branch_id')) {
            $this->ensureBranchAccess((int) $request->branch_id);
        }

        $storedImage = $this->storeProductImage($request, $product);

        $product->update(array_merge(
            $request->only(['name', 'sku', 'barcode', 'category_id', 'cost_price', 'base_price', 'description']),
            [
                'meta' => array_merge($product->meta ?? [], [
                    'image_url' => $storedImage ?? $request->input('image_url'),
                ]),
            ]
        ));

        if ($request->filled('branch_id')) {
            ProductBranch::updateOrCreate([
                'product_id' => $product->id,
                'branch_id' => $request->branch_id,
            ], [
                'stock' => $request->stock ?: 0,
                'selling_price' => $request->selling_price ?: $product->base_price,
                'margin_percent' => $product->base_price > 0 ? round((($request->selling_price ?: $product->base_price) - $product->cost_price) / max($product->base_price, 1) * 100, 2) : 0,
                'is_active' => true,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->ensureProductAccess($product);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function ensureProductAccess(Product $product): void
    {
        $hasAccess = $product->branches()
            ->whereIn('branch_id', $this->accessibleBranchIds())
            ->exists();

        if (! $hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }
    }

    private function storeProductImage(Request $request, ?Product $product = null): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('uploads/products');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');
        $filename = now()->format('YmdHis').'-'.Str::random(10).'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        $currentImage = data_get($product?->meta, 'image_url');
        if (is_string($currentImage) && Str::startsWith($currentImage, '/uploads/products/')) {
            $existingPath = public_path(ltrim($currentImage, '/'));

            if (File::exists($existingPath)) {
                File::delete($existingPath);
            }
        }

        return '/uploads/products/'.$filename;
    }
}
