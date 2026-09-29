<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::tersedia()
            ->with('category')
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('pembeli.catalog.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        // produk berstatus "habis" tidak boleh dibuka lewat URL langsung
        abort_unless($product->isTersedia(), 404);

        $product->load(['category', 'seller']);

        return view('pembeli.catalog.show', compact('product'));
    }
}