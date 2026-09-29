@extends('layouts.app')

@section('title', 'Produk Saya')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Produk Saya</h1>
    <a href="{{ route('penjual.products.create') }}"
        class="bg-brand-500 hover:bg-brand-600 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
        + Tambah Produk
    </a>
</div>

@include('partials.flash')

<div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
            <tr>
                <th class="px-5 py-3 font-medium">Gambar</th>
                <th class="px-5 py-3 font-medium">Nama</th>
                <th class="px-5 py-3 font-medium">Kategori</th>
                <th class="px-5 py-3 font-medium">Harga</th>
                <th class="px-5 py-3 font-medium">Stok</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 text-right font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse ($products as $product)
                <tr>
                    <td class="px-5 py-3">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="h-12 w-12 rounded-lg object-cover" />
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400 dark:bg-white/5">
                                -
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">{{ $product->name }}</td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $product->category->name }}</td>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-gray-800 dark:text-white/90">{{ $product->stock }}</td>
                    <td class="px-5 py-3">
                        @if ($product->isTersedia())
                            <span class="rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Tersedia</span>
                        @else
                            <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Habis</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-4">
                            <form method="POST" action="{{ route('penjual.products.status', $product) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                    {{ $product->isTersedia() ? 'Tandai Habis' : 'Tandai Tersedia' }}
                                </button>
                            </form>
                            <a href="{{ route('penjual.products.edit', $product) }}"
                                class="text-brand-500 hover:text-brand-600">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada produk. Klik "Tambah Produk" untuk memulai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection