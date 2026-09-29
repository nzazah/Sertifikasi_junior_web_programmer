@extends('layouts.app')

@section('title', 'Dashboard Pembeli')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->name }} (Pembeli)</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 hover:underline">Logout</button>
        </form>
    </div>
</div>
@endsection