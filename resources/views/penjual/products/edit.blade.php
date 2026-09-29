@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
@php
    $input = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $textarea = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="mb-6">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Edit Produk</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Kategori: {{ $product->category->name }}
    </p>
</div>

<div class="max-w-2xl rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="POST" action="{{ route('penjual.products.update', $product) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Nama produk<span class="text-error-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="{{ $input }}" />
            @error('name')
                <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
            <textarea name="description" rows="4" class="{{ $textarea }}">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Harga (Rp)<span class="text-error-500">*</span>
                </label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" required class="{{ $input }}" />
                @error('price')
                    <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Stok<span class="text-error-500">*</span>
                </label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required class="{{ $input }}" />
                @error('stock')
                    <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if ($product->image)
            <div>
                <p class="mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">Gambar saat ini</p>
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                    class="h-28 w-28 rounded-lg object-cover" />
            </div>
        @endif

        <div class="flex items-center gap-3">
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('penjual.products.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Batal</a>
        </div>
    </form>
</div>
@endsection