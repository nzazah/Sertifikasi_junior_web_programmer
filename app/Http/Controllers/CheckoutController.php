<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create()
    {
        $items = auth()->user()->cartItems()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('pembeli.cart.index')
                ->withErrors(['cart' => 'Keranjang Anda masih kosong.']);
        }

        if ($items->contains(fn ($item) => ! $item->isValid())) {
            return redirect()->route('pembeli.cart.index')
                ->withErrors(['cart' => 'Ada produk yang tidak tersedia atau stoknya kurang. Perbaiki dulu di keranjang.']);
        }

        $total = $items->sum(fn ($item) => $item->subtotal());

        return view('pembeli.checkout.create', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => ['required', 'string', 'max:500'],
        ]);

        try {
            $order = DB::transaction(function () use ($data) {
                $cartItems = auth()->user()->cartItems()->get();

                if ($cartItems->isEmpty()) {
                    throw ValidationException::withMessages(['cart' => 'Keranjang Anda masih kosong.']);
                }

                // kunci baris produk supaya stok terakhir tidak bisa dibeli dua orang sekaligus
                $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($cartItems as $item) {
                    $product = $products[$item->product_id];

                    if (! $product->isTersedia() || $item->quantity > $product->stock) {
                        throw ValidationException::withMessages([
                            'cart' => "Stok {$product->name} tidak mencukupi (tersisa {$product->stock}). Perbaiki jumlah di keranjang.",
                        ]);
                    }
                }

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'address' => $data['address'],
                    'status' => 'menunggu_bayar',
                    'total' => $cartItems->sum(fn ($i) => $products[$i->product_id]->price * $i->quantity),
                ]);

                foreach ($cartItems as $item) {
                    $product = $products[$item->product_id];

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item->quantity,
                    ]);

                    $product->decrement('stock', $item->quantity);
                }

                auth()->user()->cartItems()->delete();

                return $order;
            });
        } catch (ValidationException $e) {
            return redirect()->route('pembeli.cart.index')->withErrors($e->errors());
        }

        return redirect()->route('pembeli.orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat. Silakan unggah bukti pembayaran.');
    }
}