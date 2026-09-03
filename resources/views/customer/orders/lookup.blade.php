@extends('layouts.app')

@section('title', 'Cek Pesanan - LUMINA')

@push('styles')
<style>
    body {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    #mainNavbar { display: none !important; }
    .footer { display: none !important; }
    .mobile-bottom-nav { display: none !important; }
</style>
@endpush

@section('content')
@php
    $lang = 'id';
@endphp

<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')

    <div class="min-h-screen bg-zinc-50 py-8 pt-24 md:pt-28">
        <div class="mx-auto max-w-md px-6 md:px-10">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-black text-white">
                    <i class="fas fa-search text-lg"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-black">Cek Pesanan</h1>
                <p class="mt-2 text-sm text-zinc-500">Masukkan nomor pesanan dan email Anda untuk melihat detail pesanan.</p>
            </div>

            @if(session('error'))
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-red-900">Pesanan Tidak Ditemukan</p>
                            <p class="text-xs text-red-800 mt-1">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm border border-zinc-100">
                <form action="{{ route('customer.orders.lookup.process') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="order_number" class="block text-sm font-semibold text-black mb-2">Nomor Pesanan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-receipt text-zinc-400 text-xs"></i>
                            </div>
                            <input type="text" name="order_number" id="order_number"
                                value="{{ old('order_number') }}"
                                placeholder="Contoh: NP-20260101-ABC12"
                                class="block w-full rounded-xl border border-zinc-200 bg-zinc-50 pl-9 pr-3 py-2.5 text-sm text-black placeholder-zinc-400 focus:border-black focus:bg-white focus:outline-none focus:ring-1 focus:ring-black/10 transition-colors"
                                required>
                        </div>
                        @error('order_number')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-black mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-zinc-400 text-xs"></i>
                            </div>
                            <input type="email" name="email" id="email"
                                value="{{ old('email') }}"
                                placeholder="email@contoh.com"
                                class="block w-full rounded-xl border border-zinc-200 bg-zinc-50 pl-9 pr-3 py-2.5 text-sm text-black placeholder-zinc-400 focus:border-black focus:bg-white focus:outline-none focus:ring-1 focus:ring-black/10 transition-colors"
                                required>
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl bg-black px-4 py-3 text-sm font-semibold text-white transition hover:bg-black/85 focus:outline-none focus:ring-2 focus:ring-black/20">
                        <i class="fas fa-search mr-2"></i>Cek Pesanan
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-zinc-500">
                    Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-black hover:underline">Daftar</a>
                    atau <a href="{{ route('login') }}" class="font-semibold text-black hover:underline">Login</a> untuk melihat semua pesanan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
