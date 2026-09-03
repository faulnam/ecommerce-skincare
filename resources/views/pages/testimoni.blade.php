@extends('layouts.app')

@php
    $jsonPath = public_path('translation/testimoni.json');
    $trans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

@section('title', $trans['meta_title'][$lang] ?? 'Testimoni - LUMINA Skincare')
@section('og_description', $trans['meta_description'][$lang] ?? 'Ribuan pelanggan puas dengan formula dan hasil perawatan LUMINA Skincare. Baca ulasan nyata pelanggan kami.')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <span class="section-badge">{{ $trans['badge'][$lang] ?? 'Testimoni' }}</span>
        <h1 class="page-title">{{ $trans['hero_title_1'][$lang] ?? 'Apa Kata' }} <span class="text-primary">{{ $trans['hero_title_2'][$lang] ?? 'Mereka?' }}</span></h1>
        <p class="page-subtitle">{{ $trans['hero_desc'][$lang] ?? 'Review jujur dari pelanggan setia LUMINA Skincare' }}</p>
    </div>
</section>

<!-- Stats -->
<section class="py-4 bg-gray-light">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-4 col-md-4">
                <div class="stat-box">
                    <span class="stat-number">{{ $stats['total_reviews'] }}+</span>
                    <span class="stat-label">{{ $trans['stat_reviews'][$lang] ?? 'Review' }}</span>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-box">
                    <span class="stat-number">{{ $stats['avg_rating'] }}</span>
                    <span class="stat-label">{{ $trans['stat_rating'][$lang] ?? 'Rating ⭐' }}</span>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-box">
                    <span class="stat-number">{{ $stats['satisfaction_rate'] }}%</span>
                    <span class="stat-label">{{ $trans['stat_satisfied'][$lang] ?? 'Puas' }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        @if($testimonials->count() > 0)
            <div class="row g-4">
                @foreach($testimonials as $testimonial)
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card h-100">
                            <div class="testimonial-rating mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <p class="testimonial-content">"{{ $testimonial->content }}"</p>
                            <div class="testimonial-author">
                                <img src="{{ $testimonial->user->avatar_url }}" alt="{{ $testimonial->user->name }}" class="author-avatar-img">
                                <div>
                                    <h6 class="mb-0">{{ $testimonial->user->name }}</h6>
                                    <small class="text-gray">{{ $testimonial->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($testimonials->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $testimonials->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-comments fa-4x text-gray mb-3"></i>
                <h5>{{ $trans['empty_title'][$lang] ?? 'Belum Ada Testimoni' }}</h5>
                <p class="text-gray">{{ $trans['empty_desc'][$lang] ?? 'Jadilah yang pertama memberikan review!' }}</p>
            </div>
        @endif
    </div>
</section>
@endsection