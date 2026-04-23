<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    public function index(?Category $editing = null)
    {
        if (! Schema::hasTable('categories')) {
            return view('categories.index', [
                'categories' => collect(),
                'categoryTableMissing' => true,
                'editing' => null,
            ]);
        }

        return view('categories.index', [
            'categories' => Category::orderBy('name')->get(),
            'categoryTableMissing' => false,
            'editing' => $editing,
        ]);
    }

    public function edit(Category $category)
    {
        return $this->index($category);
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('categories')) {
            return redirect()->route('categories.index')->with('error', 'Tabel kategori belum tersedia. Jalankan migrasi kategori terlebih dahulu.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function update(Request $request, Category $category)
    {
        if (! Schema::hasTable('categories')) {
            return redirect()->route('categories.index')->with('error', 'Tabel kategori belum tersedia.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if (! Schema::hasTable('categories')) {
            return redirect()->route('categories.index')->with('error', 'Tabel kategori belum tersedia.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
