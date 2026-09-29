@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
@php
    $textarea = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]';
@endphp

<div class="mb-6">
    <a href="{{ route('pembeli.cart.index') }}"
        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Kembali ke keranjang</a>
    <h1 class="mt-2 text-xl font-semibold text-gray-800 dark:text-white/90">Checkout</h1>
</div>

@include('partials.flash')

<form method="POST" action="{{ route('pembeli.checkout.store') }}" class="grid gap-6 lg:grid-cols-3">
    @csrf

    <div class="{{ $card }} lg:col-span-2">
        <h2 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Alamat Pengiriman</h2>

        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Alamat lengkap<span class="text-error-500">*</span>
        </label>
        <textarea name="address" rows="4" required class="{{ $textarea }}"
            placeholder="Nama jalan, nomor rumah, kecamatan, kota, kode pos">{{ old('address') }}</textarea>
        @error('address')
            <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
        @enderror

        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            Pembayaran dilakukan setelah pesanan dibuat. Unggah bukti pembayaran di halaman detail pesanan.
        </p>
    </div>

    <div class="{{ $card }}">
        <h2 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Ringkasan Pesanan</h2>

        <div class="space-y-2">
            @foreach ($items as $item)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-700 dark:text-gray-300">{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                    <span class="text-gray-800 dark:text-white/90">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-800">
            <span class="text-gray-600 dark:text-gray-400">Total</span>
            <span class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Rp {{ number_format($total, 0, ',', '.') }}
            </span>
        </div>

        <button type="submit"
            class="bg-brand-500 hover:bg-brand-600 mt-5 w-full rounded-lg px-5 py-3 text-sm font-medium text-white transition">
            Buat Pesanan
        </button>
    </div>
</form>
@endsection