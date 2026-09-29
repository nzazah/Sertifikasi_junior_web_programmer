@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $input = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-28 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
@endphp

<div class="mb-6">
    <a href="{{ route('pembeli.catalog.index') }}"
        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Kembali ke katalog</a>
</div>

@include('partials.flash')

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<div class="grid gap-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:grid-cols-2">
    <div>
        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                class="w-full rounded-xl object-cover" />
        @else
            <div class="flex h-64 w-full items-center justify-center rounded-xl bg-gray-100 text-sm text-gray-400 dark:bg-white/5">
                Tanpa gambar
            </div>
        @endif
    </div>

    <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name }}</p>
        <h1 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $product->name }}</h1>
        <p class="mt-3 text-2xl font-semibold text-gray-800 dark:text-white/90">
            Rp {{ number_format($product->price, 0, ',', '.') }}
        </p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $product->stock > 0 ? 'Stok tersedia: ' . $product->stock : 'Stok habis' }}
            &middot; Dijual oleh {{ $product->seller->name }}
        </p>

        @if ($product->description)
            <p class="mt-4 whitespace-pre-line text-sm text-gray-600 dark:text-gray-400">{{ $product->description }}</p>
        @endif

        @if ($product->stock > 0)
            <form method="POST" action="{{ route('pembeli.cart.store') }}" class="mt-6 flex items-center gap-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" required
                    class="{{ $input }}" />
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                    Tambah ke Keranjang
                </button>
            </form>
        @else
            <p class="mt-6 text-sm font-medium text-red-600">Produk ini sedang habis.</p>
        @endif
    </div>
</div>
@endsection