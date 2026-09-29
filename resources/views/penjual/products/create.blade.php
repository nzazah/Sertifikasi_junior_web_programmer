@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
@php
    $input = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $textarea = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="mb-6">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Tambah Produk</h1>
</div>

@include('partials.flash')

<div class="max-w-2xl rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    @if ($categories->isEmpty())
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Belum ada kategori. Buat kategori dulu di
            <a href="{{ route('penjual.categories.index') }}" class="text-brand-500 hover:text-brand-600">halaman Kategori</a>
            sebelum menambah produk.
        </p>
    @else
        <form method="POST" action="{{ route('penjual.products.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Kategori<span class="text-error-500">*</span>
                </label>
                <select name="category_id" required class="{{ $input }}">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Nama produk<span class="text-error-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required class="{{ $input }}" />
                @error('name')
                    <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                <textarea name="description" rows="4" class="{{ $textarea }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Harga (Rp)<span class="text-error-500">*</span>
                    </label>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" required class="{{ $input }}" />
                    @error('price')
                        <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Stok<span class="text-error-500">*</span>
                    </label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required class="{{ $input }}" />
                    @error('stock')
                        <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Gambar produk</label>
                <input type="file" name="image" accept="image/*"
                    class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 dark:text-gray-400 dark:file:bg-white/10 dark:file:text-white/90" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format gambar (JPG, PNG, dsb), maksimal 2 MB.</p>
                @error('image')
                    <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                    Simpan Produk
                </button>
                <a href="{{ route('penjual.products.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Batal</a>
            </div>
        </form>
    @endif
</div>
@endsection