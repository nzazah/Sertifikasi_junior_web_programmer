@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('content')
@php
    $input = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="mb-6">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Kategori Produk</h1>
</div>

@include('partials.flash')

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Form tambah -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h2 class="mb-4 text-base font-medium text-gray-800 dark:text-white/90">Tambah Kategori</h2>

            <form method="POST" action="{{ route('penjual.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama kategori</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: Elektronik" class="{{ $input }}" />
                    @error('name')
                        <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 w-full rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Simpan
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar kategori -->
    <div class="lg:col-span-2">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Nama</th>
                        <th class="px-5 py-3 font-medium">Jumlah Produk</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-5 py-3 text-gray-800 dark:text-white/90">{{ $category->name }}</td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $category->products_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('penjual.categories.edit', $category) }}"
                                        class="text-brand-500 hover:text-brand-600">Edit</a>
                                    <form method="POST" action="{{ route('penjual.categories.destroy', $category) }}"
                                        onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-6 text-center text-gray-500 dark:text-gray-400">
                                Belum ada kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection