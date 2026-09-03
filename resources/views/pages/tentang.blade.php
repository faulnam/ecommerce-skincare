@extends('layouts.app')

@section('title', 'Tentang Kami - LUMINA Skincare')
@section('og_description', 'Kenali LUMINA Skincare lebih dekat — brand perawatan kulit dermatologis Indonesia yang mengutamakan kualitas, keamanan BPOM, dan hasil nyata.')

@section('content')
<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <span class="section-badge">Tentang Kami</span>
    <h1 class="page-title">Kenali <span class="text-primary">LUMINA Skincare</span></h1>
    <p class="page-subtitle">Solusi perawatan kulit terpercaya untuk kilau alami dan skin barrier sehat</p>
    </div>
</section>

<!-- Story Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
             <img src="{{ asset('storage/model-1.jpg') }}"
                 alt="LUMINA Skincare" class="img-fluid rounded-4">
            </div>
            <div class="col-lg-6">
                <h2 class="section-title">Kisah Kami</h2>
                <p class="text-gray mb-4">
                    LUMINA Skincare lahir dari semangat menghadirkan produk perawatan kulit berstandar dermatologi yang aman, efektif, dan terjangkau bagi masyarakat Indonesia. Berawal dari kepedulian terhadap tingginya permasalahan skin barrier dan sensitivitas kulit di iklim tropis,
                    kami fokus mengkurasi bahan aktif yang terbukti secara klinis.
                </p>
                <p class="text-gray mb-4">
                    Nama "LUMINA" mencerminkan cahaya, kejernihan, dan vitalitas kulit yang sehat.
                    Kami percaya setiap orang berhak memiliki rutinitas perawatan kulit yang menenangkan tanpa rasa khawatir akan bahan kimia berbahaya.
                </p>
                <div class="story-stats">
                    <div class="story-stat">
                        <span class="stat-number">2023</span>
                        <span class="stat-label">Tahun Berdiri</span>
                    </div>
                    <div class="story-stat">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">Teruji BPOM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision Mission -->
<section class="py-5 bg-gray-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="vm-card">
                    
                    <h3>Visi Kami</h3>
                    <p class="text-gray mb-0">
                        Menjadi brand perawatan kulit terpercaya di Indonesia yang mendefinisikan standar kecantikan sehat, inklusif, dan berbasis sains dermatologi.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="vm-card">
                    
                    <h3>Misi Kami</h3>
                    <ul class="text-gray mb-0 ps-3">
                        <li>Menghadirkan serum, moisturizer, cleanser, dan sunscreen bersertifikasi resmi BPOM</li>
                        <li>Memberikan edukasi kesehatan kulit yang transparan dan berbasis riset ilmiah</li>
                        <li>Menyediakan konsultasi ramah bersama Beauty Advisor untuk setiap tipe kulit</li>
                        <li>Menjamin pengalaman belanja online yang aman, cepat, dan terpercaya</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Process -->
<section class="py-5 bg-gray-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge">Process</span>
            <h2 class="section-title">From Product Selection to Your Hands</h2>
        </div>
        
        <div class="process-timeline">
            <div class="process-item">
                <div class="process-number">1</div>
                <div class="process-content">
                    <h5>Product Curation</h5>
                    <p class="small text-gray mb-0">We select brands and gear specifications with proven quality</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-number">2</div>
                <div class="process-content">
                    <h5>Quality Check</h5>
                    <p class="small text-gray mb-0">Each product is checked for physical condition and completeness before display</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-number">3</div>
                <div class="process-content">
                    <h5>Product Recommendation</h5>
                    <p class="small text-gray mb-0">Our team helps choose gear according to level, position, and playing needs</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-number">4</div>
                <div class="process-content">
                    <h5>Safe Packaging</h5>
                    <p class="small text-gray mb-0">Products are packed neatly to remain safe during the shipping process</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-number">5</div>
                <div class="process-content">
                    <h5>Fast Delivery</h5>
                    <p class="small text-gray mb-0">Orders are shipped on time so you can immediately get on the field</p>
                </div>
            </div>
        </div>
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
    
    .story-stats {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .story-stat {
        text-align: center;
    }
    
    .story-stat .stat-number {
        display: block;
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
    }
    
    .story-stat .stat-label {
        color: var(--gray);
        font-size: 0.875rem;
    }
    
    .vm-card {
        background: var(--white);
        padding: 2rem;
        border-radius: var(--radius);
        height: 100%;
    }
    
    .vm-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .vm-icon-accent {
        background: var(--accent-light);
        color: var(--accent);
    }
    
    .value-card {
        background: var(--white);
        padding: 1.5rem;
        border-radius: var(--radius);
        text-align: center;
        border: 1px solid var(--gray-light);
        transition: var(--transition);
        height: 100%;
    }
    
    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow);
    }
    
    .value-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin: 0 auto 1rem;
    }
    
    .value-icon-orange { background: var(--accent-light); color: var(--accent); }
    .value-icon-blue { background: #dbeafe; color: #3b82f6; }
    .value-icon-purple { background: #f3e8ff; color: #9333ea; }
    
    .process-timeline {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }
    
    .process-item {
        flex: 1;
        min-width: 180px;
        max-width: 200px;
        text-align: center;
        padding: 1.5rem 1rem;
        background: var(--white);
        border-radius: var(--radius);
        position: relative;
    }
    
    .process-number {
        width: 40px;
        height: 40px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin: 0 auto 1rem;
    }
    
    .cta-section {
        background: var(--primary);
        padding: 4rem 0;
    }
    
    .cta-content {
        text-align: center;
        color: white;
    }
    
    .cta-content h2 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .cta-content p {
        opacity: 0.9;
        margin-bottom: 1.5rem;
    }
    
    /* Responsive untuk tablet */
    @media (max-width: 991.98px) {
        .page-hero {
            padding: 3rem 0;
        }
        
        .page-title {
            font-size: 2rem;
        }
        
        .story-stats {
            gap: 1.5rem;
        }
        
        .story-stat .stat-number {
            font-size: 1.75rem;
        }
        
        .vm-card {
            padding: 1.5rem;
        }
        
        .process-item {
            min-width: 150px;
            max-width: 180px;
            padding: 1.25rem 0.75rem;
        }
        
        .cta-section {
            padding: 3rem 0;
        }
        
        .cta-content h2 {
            font-size: 1.75rem;
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
        
        .section-title {
            font-size: 1.25rem;
        }
        
        .story-stats {
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }
        
        .story-stat {
            flex: 0 0 calc(33.333% - 0.75rem);
        }
        
        .story-stat .stat-number {
            font-size: 1.5rem;
        }
        
        .story-stat .stat-label {
            font-size: 0.75rem;
        }
        
        .vm-card {
            padding: 1.25rem;
        }
        
        .vm-card h3 {
            font-size: 1.125rem;
        }
        
        .vm-card ul {
            font-size: 0.9rem;
        }
        
        .vm-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        
        .value-card {
            padding: 1.25rem;
        }
        
        .value-icon {
            width: 50px;
            height: 50px;
            font-size: 1rem;
        }
        
        .value-card h5 {
            font-size: 1rem;
        }
        
        .process-timeline {
            flex-direction: column;
            align-items: center;
        }
        
        .process-item {
            min-width: 100%;
            max-width: 100%;
            display: flex;
            align-items: center;
            text-align: left;
            padding: 1rem;
            gap: 1rem;
        }
        
        .process-number {
            margin: 0;
            flex-shrink: 0;
        }
        
        .process-content {
            flex: 1;
        }
        
        .process-content h5 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        
        .cta-section {
            padding: 2rem 0;
        }
        
        .cta-content h2 {
            font-size: 1.25rem;
        }
        
        .cta-content p {
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .cta-content .btn {
            padding: 0.5rem 1.5rem;
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
        
        .section-badge {
            font-size: 0.7rem;
        }
        
        .story-stat {
            flex: 0 0 calc(50% - 0.5rem);
        }
        
        .story-stat .stat-number {
            font-size: 1.25rem;
        }
        
        .vm-card {
            padding: 1rem;
        }
        
        .vm-card p,
        .vm-card ul {
            font-size: 0.85rem;
        }
        
        .process-item {
            padding: 0.75rem;
        }
        
        .process-number {
            width: 35px;
            height: 35px;
            font-size: 0.875rem;
        }
        
        .cta-content h2 {
            font-size: 1.125rem;
        }
    }
</style>
@endpush
