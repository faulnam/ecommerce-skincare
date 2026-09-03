@extends('layouts.app')

@section('title', 'Gallery - LUMINA')
@section('og_description', 'Galeri foto dan momen LUMINA — event komunitas, aktivitas skincare, dan koleksi produk terbaik kami.')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <span class="section-badge">Gallery</span>
        <h1 class="page-title">Our <span class="text-primary">Moments & Activities</span></h1>
    <p class="page-subtitle">Documentation of LUMINA products, events, and activities</p>
    </div>
</section>

<!-- Gallery Grid -->
<section class="py-5">
    <div class="container">
        @if($galleries->count() > 0)
            <div class="gallery-grid">
                @foreach($galleries as $gallery)
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#galleryModal{{ $gallery->id }}">
                        @if($gallery->isImage())
                            <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}">
                            <div class="gallery-hover">
                                <i class="fas fa-expand"></i>
                                <span>{{ $gallery->title }}</span>
                            </div>
                        @else
                            <div class="gallery-video-thumb">
                                <i class="fab fa-instagram fa-3x text-white"></i>
                                <span>{{ $gallery->title }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="galleryModal{{ $gallery->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title">{{ $gallery->title }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    @if($gallery->isImage())
                                        <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="img-fluid w-100">
                                    @else
                                        <div class="ratio ratio-16x9">
                                            {!! $gallery->embed_url !!}
                                        </div>
                                    @endif
                                </div>
                                @if($gallery->description)
                                    <div class="modal-footer border-0">
                                        <p class="text-gray mb-0 w-100">{{ $gallery->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-images fa-4x text-gray mb-3"></i>
                <h5>Gallery Still Empty</h5>
                <p class="text-gray">Documentation will be added soon</p>
            </div>
        @endif
    </div>
</section>


@endsection

@push('styles')
<style>
    .page-hero {
        background: var(--white);
        padding: 4rem 0;
        text-align: center;
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: var(--gray);
        font-size: 1.125rem;
    }
    
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
    
    .gallery-item {
        position: relative;
        /* Rasio 4:5 seperti Instagram */
        aspect-ratio: 4 / 5;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        background: var(--gray-light);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .gallery-item:hover {
        box-shadow: 0 12px 32px rgba(0,0,0,0.2);
        transform: translateY(-6px) scale(1.02);
    }
    
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    
    .gallery-item:hover img {
        transform: scale(1.1);
    }
    
    .gallery-hover {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding: 1.5rem;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
        gap: 0.5rem;
    }
    
    .gallery-hover i {
        font-size: 2.5rem;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .gallery-hover span {
        font-weight: 600;
        font-size: 1rem;
        text-align: center;
    }
    
    .gallery-item:hover .gallery-hover {
        opacity: 1;
    }
    
    .gallery-video-thumb {
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, #833ab4, #fd1d1d, #fcb045);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        color: white;
    }
    
    .gallery-video-thumb span {
        font-weight: 600;
        font-size: 1rem;
    }
    
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .modal-body img {
        max-height: 80vh;
        object-fit: contain;
    }
    
    /* Responsive untuk tablet */
    @media (max-width: 991.98px) {
        .page-hero {
            padding: 3rem 0;
        }
        
        .page-title {
            font-size: 2rem;
        }
        
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
    }
    
    /* Responsive untuk mobile */
    @media (max-width: 767.98px) {
        .page-hero {
            padding: 2.5rem 0;
        }
        
        .page-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            font-size: 0.9rem;
        }
        
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .gallery-hover i {
            font-size: 1.75rem;
        }
        
        .gallery-hover span {
            font-size: 0.85rem;
            padding: 0 0.5rem;
        }
        
        .gallery-video-thumb i {
            font-size: 2.5rem !important;
        }
        
        .gallery-video-thumb span {
            font-size: 0.85rem;
            padding: 0 0.5rem;
            text-align: center;
        }
        
        /* Instagram CTA mobile */
        .bg-gray-light h4 {
            font-size: 1.125rem;
        }
        
        .bg-gray-light p {
            font-size: 0.875rem;
        }
        
        .bg-gray-light .btn {
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
        }
        
        .bg-gray-light .fa-3x {
            font-size: 2rem !important;
        }
        
        /* Modal mobile */
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .modal-title {
            font-size: 1rem;
        }
        
        .modal-footer p {
            font-size: 0.875rem;
        }
    }
    
    /* Responsive untuk mobile kecil */
    @media (max-width: 575.98px) {
        .page-hero {
            padding: 2rem 0;
        }
        
        .page-title {
            font-size: 1.25rem;
        }
        
        .gallery-grid {
            gap: 0.5rem;
        }
        
        .gallery-hover i {
            font-size: 1.5rem;
        }
        
        .gallery-hover span {
            font-size: 0.75rem;
        }
        
        .gallery-video-thumb span {
            font-size: 0.75rem;
        }
        
        .bg-gray-light h4 {
            font-size: 1rem;
        }
    }
</style>
@endpush
