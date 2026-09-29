@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')
@php
    $input = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Katalog Produk</h1>
    <a href="{{ route('pembeli.cart.index') }}"
        class="bg-brand-500 hover:bg-brand-600 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
        Lihat Keranjang
    </a>
</div>

@include('partials.flash')

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<form method="GET" action="{{ route('pembeli.catalog.index') }}" class="mb-6 flex flex-col gap-3 sm:flex-row">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="{{ $input }}" />
    <select name="category" class="{{ $input }} sm:max-w-xs">
        <option value="">Semua kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <button type="submit"
        class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
        Cari
    </button>
</form>

@if ($products->isEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">
        Belum ada produk yang tersedia.
    </div>
@else
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($products as $product)
            <div class="flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <a href="{{ route('pembeli.catalog.show', $product) }}">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="h-44 w-full object-cover" />
                    @else
                        <div class="flex h-44 w-full items-center justify-center bg-gray-100 text-sm text-gray-400 dark:bg-white/5">
                            Tanpa gambar
                        </div>
                    @endif
                </a>

                <div class="flex flex-1 flex-col p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->category->name }}</p>
                    <a href="{{ route('pembeli.catalog.show', $product) }}"
                        class="mt-1 font-medium text-gray-800 hover:text-brand-500 dark:text-white/90">
                        {{ $product->name }}
                    </a>
                    <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $product->stock > 0 ? 'Stok: ' . $product->stock : 'Stok habis' }}
                    </p>

                    <form method="POST" action="{{ route('pembeli.cart.store') }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}" />
                        <input type="hidden" name="quantity" value="1" />
                        <button type="submit" @disabled($product->stock < 1)
                            class="bg-brand-500 hover:bg-brand-600 w-full rounded-lg px-4 py-2.5 text-sm font-medium text-white transition disabled:cursor-not-allowed disabled:opacity-50">
                            + Keranjang
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endif
@endsection