<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = auth()->user()->products()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('penjual.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('penjual.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        auth()->user()->products()->create($data);

        return redirect()->route('penjual.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $this->authorizeOwner($product);

        return view('penjual.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeOwner($product);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('penjual.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function toggleStatus(Product $product)
    {
        $this->authorizeOwner($product);

        $product->update([
            'status' => $product->isTersedia() ? 'habis' : 'tersedia',
        ]);

        return back()->with('success', 'Status produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeOwner($product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('penjual.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function authorizeOwner(Product $product): void
    {
        abort_unless($product->user_id === auth()->id(), 403, 'Ini bukan produk milik Anda.');
    }
}