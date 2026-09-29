@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
@php
    $input = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="mb-6">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Edit Kategori</h1>
</div>

<div class="max-w-lg rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="POST" action="{{ route('penjual.categories.update', $category) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama kategori</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="{{ $input }}" />
            @error('name')
                <p class="text-error-500 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                Simpan
            </button>
            <a href="{{ route('penjual.categories.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Batal</a>
        </div>
    </form>
</div>
@endsection