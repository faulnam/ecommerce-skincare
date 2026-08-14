@props([
    'variant' => 'default', // default, dark, white
    'height' => null,
    'class' => '',
    'showText' => false
])

@php
    $logoPath = match($variant) {
        'dark' => config('branding.logo_dark'),
        'white' => config('branding.logo_white'),
        default => config('branding.logo'),
    };
    
    $defaultHeight = match(true) {
        str_contains(request()->path(), 'admin') => config('branding.logo_height.sidebar'),
        str_contains(request()->path(), 'courier') => config('branding.logo_height.sidebar'),
        default => config('branding.logo_height.navbar'),
    };
    
    $logoHeight = $height ?? $defaultHeight;
@endphp

@if(file_exists(public_path($logoPath)))
    <img 
        src="{{ asset($logoPath) }}" 
        alt="{{ config('branding.name') }}" 
        height="{{ $logoHeight }}"
        {{ $attributes->merge(['class' => 'brand-logo ' . $class]) }}
    >
    @if($showText)
        <span class="brand-text ms-2">{{ config('branding.name') }}</span>
    @endif
@else
    {{-- Fallback ke text jika logo belum ada --}}
    <span {{ $attributes->merge(['class' => 'brand-text-only ' . $class]) }}>
        <i class="fas fa-leaf me-1"></i>{{ config('branding.name') }}
    </span>
@endif
