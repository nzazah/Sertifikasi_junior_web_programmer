<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerOrderController extends Controller
{
    public function index(Request $request)
    {
        $mine = fn ($query) => $query->where('user_id', auth()->id());

        $orders = Order::whereHas('items.product', $mine)
            ->with([
                'user',
                // hanya item milik penjual ini
                'items' => fn ($query) => $query->whereHas('product', $mine),
            ])
            ->when(
                in_array($request->status, Order::STATUSES, true),
                fn ($query) => $query->where('status', $request->status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('penjual.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $items = $this->sellerItems($order);

        $order->load('user');
        $hasOtherSellers = $order->items()->count() > $items->count();

        return view('penjual.orders.show', compact('order', 'items', 'hasOtherSellers'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->sellerItems($order); // pastikan order ini memuat produk penjual

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Order::STATUSES)],
        ]);

        $updated = DB::transaction(function () use ($order, $data) {
            // kunci baris order supaya pembatalan tidak bisa mengembalikan stok dua kali
            $fresh = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! in_array($data['status'], $fresh->allowedTransitions(), true)) {
                return false;
            }

            if ($data['status'] === 'dibatalkan') {
                foreach ($fresh->items as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $fresh->update(['status' => $data['status']]);

            return true;
        });

        if (! $updated) {
            return back()->withErrors(['status' => 'Perubahan status tidak diizinkan dari status saat ini.']);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    /** Item pesanan milik penjual yang login; 403 kalau pesanan tidak memuat produknya. */
    private function sellerItems(Order $order)
    {
        $items = $order->items()
            ->whereHas('product', fn ($query) => $query->where('user_id', auth()->id()))
            ->get();

        abort_if($items->isEmpty(), 403, 'Pesanan ini tidak memuat produk Anda.');

        return $items;
    }
}