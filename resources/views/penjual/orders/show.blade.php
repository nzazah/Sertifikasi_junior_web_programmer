@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
@php
    $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]';
    $actionLabels = [
        'diproses' => 'Terima & Proses',
        'dikirim' => 'Tandai Dikirim',
        'selesai' => 'Tandai Selesai',
        'dibatalkan' => 'Batalkan Pesanan',
    ];
@endphp

<div class="mb-6">
    <a href="{{ route('penjual.orders.index') }}"
        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Kembali ke pesanan</a>
    <div class="mt-2 flex flex-wrap items-center gap-3">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $order->number() }}</h1>
        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $order->statusBadge() }}">{{ $order->statusLabel() }}</span>
    </div>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
</div>

@include('partials.flash')

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-3">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] lg:col-span-2">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3 font-medium">Produk</th>
                    <th class="px-5 py-3 font-medium">Harga</th>
                    <th class="px-5 py-3 font-medium">Jumlah</th>
                    <th class="px-5 py-3 text-right font-medium">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @foreach ($items as $item)
                    <tr>
                        <td class="px-5 py-3 text-gray-800 dark:text-white/90">{{ $item->product_name }}</td>
                        <td class="px-5 py-3 text-gray-800 dark:text-white/90">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-gray-800 dark:text-white/90">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right text-gray-800 dark:text-white/90">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="flex items-center justify-end gap-6 border-t border-gray-200 px-5 py-4 dark:border-gray-800">
            <span class="text-gray-600 dark:text-gray-400">Subtotal produk Anda</span>
            <span class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Rp {{ number_format($items->sum(fn ($item) => $item->subtotal()), 0, ',', '.') }}
            </span>
        </div>
        @if ($hasOtherSellers)
            <p class="border-t border-gray-200 px-5 py-3 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400">
                Pesanan ini juga berisi produk dari penjual lain. Total pesanan: Rp {{ number_format($order->total, 0, ',', '.') }}.
            </p>
        @endif
    </div>

    <div class="space-y-6">
        {{-- Ubah status --}}
        <div class="{{ $card }}">
            <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Status Pesanan</h2>

            @if ($order->status === 'menunggu_bayar')
                <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">Menunggu pembeli mengunggah bukti pembayaran.</p>
            @elseif ($order->status === 'menunggu_konfirmasi')
                <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">Periksa bukti pembayaran di bawah sebelum memproses pesanan.</p>
            @endif

            <div class="space-y-2">
                @forelse ($order->allowedTransitions() as $next)
                    <form method="POST" action="{{ route('penjual.orders.status', $order) }}"
                        @if ($next === 'dibatalkan') onsubmit="return confirm('Batalkan pesanan ini? Stok produk akan dikembalikan.')" @endif>
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next }}" />
                        <button type="submit"
                            class="{{ $next === 'dibatalkan' ? 'border border-red-300 text-red-600 hover:bg-red-50' : 'bg-brand-500 hover:bg-brand-600 text-white' }} w-full rounded-lg px-4 py-2.5 text-sm font-medium transition">
                            {{ $actionLabels[$next] }}
                        </button>
                    </form>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status ini sudah final.</p>
                @endforelse
            </div>
        </div>

        <div class="{{ $card }}">
            <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Pembeli & Alamat</h2>
            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $order->user->name }}</p>
            <p class="mt-1 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $order->address }}</p>
        </div>

        <div class="{{ $card }}">
            <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Bukti Pembayaran</h2>
            @if ($order->payment_proof)
                <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank">
                    <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti pembayaran"
                        class="w-full rounded-lg border border-gray-200 object-cover dark:border-gray-700" />
                </a>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada bukti pembayaran.</p>
            @endif
        </div>
    </div>
</div>
@endsection