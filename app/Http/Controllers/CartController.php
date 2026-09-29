<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = auth()->user()->cartItems()
            ->with('product.category')
            ->latest()
            ->get();

        // total hanya menghitung item yang masih valid (tersedia & stok cukup)
        $total = $items->filter->isValid()->sum(fn ($item) => $item->subtotal());

        return view('pembeli.cart.index', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (! $product->isTersedia() || $product->stock < 1) {
            return back()->withErrors(['quantity' => 'Produk ini sedang tidak tersedia.']);
        }

        $item = auth()->user()->cartItems()->firstOrNew(['product_id' => $product->id]);

        $inCart = $item->exists ? $item->quantity : 0;
        $newQty = $inCart + $data['quantity'];

        if ($newQty > $product->stock) {
            return back()->withErrors([
                'quantity' => "Stok {$product->name} hanya {$product->stock}. Di keranjang Anda sudah ada {$inCart}.",
            ]);
        }

        $item->quantity = $newQty;
        $item->save();

        return redirect()->route('pembeli.cart.index')
            ->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeOwner($cartItem);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = $cartItem->product;

        if (! $product->isTersedia() || $data['quantity'] > $product->stock) {
            return back()->withErrors([
                'quantity' => "Stok {$product->name} tersisa {$product->stock}.",
            ]);
        }

        $cartItem->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(CartItem $cartItem)
    {
        $this->authorizeOwner($cartItem);

        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    private function authorizeOwner(CartItem $cartItem): void
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);
    }
}