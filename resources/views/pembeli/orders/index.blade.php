@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Pesanan Saya</h1>
    <a href="{{ route('pembeli.catalog.index') }}" class="text-sm text-brand-500 hover:text-brand-600">
        + Belanja lagi
    </a>
</div>

@include('partials.flash')

<div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
            <tr>
                <th class="px-5 py-3 font-medium">No. Pesanan</th>
                <th class="px-5 py-3 font-medium">Tanggal</th>
                <th class="px-5 py-3 font-medium">Item</th>
                <th class="px-5 py-3 font-medium">Total</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse ($orders as $order)
                <tr>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">{{ $order->number() }}</td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">{{ $order->items_count }} produk</td>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $order->statusBadge() }}">
                            {{ $order->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('pembeli.orders.show', $order) }}" class="text-brand-500 hover:text-brand-600">
                            {{ $order->canUploadProof() ? 'Bayar' : 'Detail' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada pesanan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection