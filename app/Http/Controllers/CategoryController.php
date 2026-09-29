<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('penjual.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        Category::create($data);

        return redirect()->route('penjual.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('penjual.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
        ]);

        $category->update($data);

        return redirect()->route('penjual.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori masih dipakai oleh produk, tidak bisa dihapus.');
        }

        $category->delete();

        return redirect()->route('penjual.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}