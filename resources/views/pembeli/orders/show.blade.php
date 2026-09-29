@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
@php
    $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]';
@endphp

<div class="mb-6">
    <a href="{{ route('pembeli.orders.index') }}"
        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Kembali ke pesanan</a>
    <div class="mt-2 flex flex-wrap items-center gap-3">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $order->number() }}</h1>
        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $order->statusBadge() }}">{{ $order->statusLabel() }}</span>
    </div>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
</div>

@include('partials.flash')

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
                @foreach ($order->items as $item)
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
            <span class="text-gray-600 dark:text-gray-400">Total</span>
            <span class="text-xl font-semibold text-gray-800 dark:text-white/90">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="space-y-6">
        <div class="{{ $card }}">
            <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Alamat Pengiriman</h2>
            <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $order->address }}</p>
        </div>

        <div class="{{ $card }}">
            <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Bukti Pembayaran</h2>

            @if ($order->canUploadProof())
                <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
                    Lakukan pembayaran sebesar
                    <span class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</span>,
                    lalu unggah bukti pembayarannya di sini.
                </p>

                <form method="POST" action="{{ route('pembeli.orders.proof', $order) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="file" name="payment_proof" accept="image/*" required
                        class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 dark:text-gray-400 dark:file:bg-white/10 dark:file:text-white/90" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">Format gambar (JPG, PNG, dsb), maksimal 2 MB.</p>
                    @error('payment_proof')
                        <p class="text-error-500 text-sm">{{ $message }}</p>
                    @enderror
                    <button type="submit"
                        class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                        Kirim Bukti Pembayaran
                    </button>
                </form>
            @elseif ($order->payment_proof)
                <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank">
                    <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti pembayaran"
                        class="w-full rounded-lg border border-gray-200 object-cover dark:border-gray-700" />
                </a>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada bukti pembayaran.</p>
            @endif
        </div>
    </div>
</div>
@endsection