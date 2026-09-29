<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('pembeli.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeOwner($order);

        $order->load('items');

        return view('pembeli.orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        $this->authorizeOwner($order);

        if (! $order->canUploadProof()) {
            return back()->withErrors([
                'payment_proof' => 'Bukti pembayaran hanya bisa diunggah saat pesanan berstatus menunggu bayar.',
            ]);
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:2048'],
        ]);

        $order->update([
            'payment_proof' => $request->file('payment_proof')->store('payment-proofs', 'public'),
            'status' => 'menunggu_konfirmasi',
        ]);

        return back()->with('success', 'Bukti pembayaran terkirim. Menunggu konfirmasi penjual.');
    }

    private function authorizeOwner(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
    }
}