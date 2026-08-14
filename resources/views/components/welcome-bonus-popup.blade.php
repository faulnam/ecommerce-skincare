@php
$isGuest = !auth()->check();
$isCustomer = auth()->check() && auth()->user()->role === 'customer';
$shouldShow = false;
$isEligibleCustomer = false;
$eventImage = null;
$eventTitle = 'PILIHAN PRODUK GRATIS';
$eventDescription = 'UNTUK PEMBELIAN PERTAMA\nSATU AKUN';

if ($isGuest || $isCustomer) {
    $isEventActive = \App\Models\Setting::where('key', 'free_event_active')->value('value') !== '0';
    $shouldShow = $isEventActive;

    if ($shouldShow) {
        $isEligibleCustomer = $isCustomer
            && !auth()->user()->welcome_bonus_claimed
            && !auth()->user()->orders()->whereNotIn('status', ['pending', 'cancelled', 'failed'])->exists();

        $eventImage = \App\Models\Setting::where('key', 'free_event_image')->value('value');
        $eventTitle = \App\Models\Setting::where('key', 'free_event_title')->value('value') ?? $eventTitle;
        $eventDescription = \App\Models\Setting::where('key', 'free_event_description')->value('value') ?? $eventDescription;
    }
}
@endphp

@if ($shouldShow)
<div class="shopee-popup-overlay" id="welcomeBonusPopup" 
     x-data="{ show: false }"
     x-show="show"
     x-cloak
     x-init="setTimeout(() => { show = true; }, 500);"
     style="display: flex;">
    
    <div class="shopee-popup-backdrop" @click="show = false"></div>

    <div class="shopee-popup-content" 
         x-show="show"
         x-transition:enter="transition-transform ease-out duration-300"
         x-transition:enter-start="scale-75 opacity-0"
         x-transition:enter-end="scale-100 opacity-100"
         x-transition:leave="transition-transform ease-in duration-200"
         x-transition:leave-start="scale-100 opacity-100"
         x-transition:leave-end="scale-75 opacity-0">
         
        <button class="shopee-close-btn" @click="show = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        
        <!-- TOP SECTION -->
        <div class="shopee-top-sec">
            <div class="shopee-badge-0d">
                <div class="s-badge-text">
                    <span class="s-fs">FREESHIP</span>
                    <div class="s-val"><span class="s-num">0</span><span class="s-cur">Rp</span></div>
                </div>
            </div>
            
            <div class="shopee-tags">
                <span class="s-tag-blk">PROMO SPESIAL</span>
                <span class="s-tag-blu">CUMA 2 HARI</span>
            </div>
            
            <div class="shopee-stage">
                <!-- Glowing effect background -->
                <div class="s-glow"></div>
                
                <!-- If image exists, show it. Otherwise show a generic gift box icon -->
                @if($eventImage)
                <img src="{{ $eventImage }}" class="s-hero-img" alt="Promo">
                @else
                <div class="s-hero-img flex items-center justify-center">
                    <i class="fas fa-gift" style="font-size: 100px; color: #ffeb3b; text-shadow: 0 10px 20px rgba(0,0,0,0.5);"></i>
                </div>
                @endif
                
                <!-- Animated Sparkles -->
                <div class="s-star s1">✦</div>
                <div class="s-star s2">✦</div>
                <div class="s-star s3">✦</div>
            </div>
        </div>
        
        <!-- BOTTOM SECTION -->
        <div class="shopee-bot-sec">
            <!-- Title -->
            <h2 class="s-title">{{ strtoupper($eventTitle) }} <span class="s-title-icon">🎁</span></h2>
            
            <!-- Red/Yellow Ribbon Banner -->
            <div class="s-ribbon-wrap">
                <div class="s-ribbon">
                    <div class="s-percent">%</div>
                    <div class="s-ribbon-text">
                        <div class="s-rb-small">EKSKLUSIF MEMBER BARU</div>
                        <div class="s-rb-large">{!! nl2br(e(strtoupper($eventDescription))) !!}</div>
                    </div>
                </div>
            </div>
            
            <!-- Vouchers -->
            <div class="s-vouchers">
                <!-- Voucher 1 -->
                <div class="s-vou v-pink">
                    <div class="v-sm">CASHBACK</div>
                    <div class="v-lg">10%</div>
                </div>
                
                <!-- Voucher 2 -->
                <div class="s-vou v-orange">
                    <div class="v-badge">VOUCHER XTRA</div>
                    <div class="v-sm">POTONGAN</div>
                    <div class="v-lg">50RB</div>
                </div>
                
                <!-- Voucher 3 -->
                <div class="s-vou v-blue">
                    <div class="v-lg">FREE</div>
                    <div class="v-sm">ONGKIR</div>
                </div>
            </div>
            
            <p class="s-tnc">(*Syarat & ketentuan berlaku. Promo khusus untuk pengguna baru aplikasi Hijab)</p>
            
            <!-- Button -->
            <div class="s-btn-wrap">
                @if($isGuest)
                    <a href="{{ route('register') }}" class="s-btn">
                        DAFTAR SEKARANG <span class="s-btn-icon"><i class="fas fa-play"></i></span>
                    </a>
                @elseif($isEligibleCustomer)
                    <button type="button" @click="show = false; setTimeout(() => document.getElementById('free-products-section').scrollIntoView({behavior: 'smooth'}), 300)" class="s-btn border-0 w-full">
                        SĂN NGAY (KLAIM) <span class="s-btn-icon"><i class="fas fa-play"></i></span>
                    </button>
                @else
                    <a href="{{ route('customer.profile.index') }}" class="s-btn">
                        LIHAT PROFIL <span class="s-btn-icon"><i class="fas fa-play"></i></span>
                    </a>
                @endif
            </div>
            
            <div class="s-footer-tag">
                <span class="text-red-500">❤️</span> Pilihan Tepat Untuk Hijab Anda
            </div>
        </div>
    </div>
</div>

<style>
/* Reset & Fonts inside popup to ensure exact match */
.shopee-popup-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.shopee-popup-backdrop {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(2px);
}

.shopee-popup-content {
    position: relative;
    width: 360px;
    max-width: 95vw;
    font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
    z-index: 100000;
    display: flex;
    flex-direction: column;
}

/* Close Button */
.shopee-close-btn {
    position: absolute;
    top: -40px;
    right: 0;
    width: 32px; height: 32px;
    background: #fff;
    border: none;
    border-radius: 50%;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
}
.shopee-close-btn svg {
    width: 16px; height: 16px;
}

/* TOP SECTION */
.shopee-top-sec {
    background: radial-gradient(circle at center, #8b0000 0%, #1a0000 100%);
    height: 200px;
    border-radius: 12px 12px 0 0;
    position: relative;
    overflow: visible;
}

/* 0Rp Badge mimicking Shopee 0đ */
.shopee-badge-0d {
    position: absolute;
    top: -15px;
    right: 15px;
    width: 80px;
    height: 80px;
    background: #ffcc00;
    clip-path: polygon(50% 0%, 61% 16%, 80% 10%, 82% 29%, 98% 35%, 89% 52%, 98% 69%, 80% 74%, 78% 93%, 60% 86%, 50% 100%, 39% 86%, 20% 93%, 22% 73%, 2% 69%, 13% 51%, 0% 34%, 18% 29%, 20% 10%, 39% 17%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 20;
    border: 2px solid #ff0000;
    transform: rotate(15deg);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
.s-badge-text {
    text-align: center;
    transform: rotate(-15deg);
    color: #ff0000;
}
.s-fs {
    display: block;
    background: #ff0000;
    color: #fff;
    font-size: 9px;
    font-weight: 800;
    padding: 2px 4px;
    border-radius: 2px;
    margin-bottom: -2px;
}
.s-val {
    display: flex;
    align-items: flex-start;
    justify-content: center;
}
.s-num { font-size: 32px; font-weight: 900; line-height: 1; letter-spacing: -2px; text-shadow: 1px 1px 0 #fff; }
.s-cur { font-size: 14px; font-weight: 800; margin-top: 4px; text-shadow: 1px 1px 0 #fff; }

/* Tags */
.shopee-tags {
    position: absolute;
    top: 15px; left: 15px;
    display: flex;
    z-index: 5;
    font-size: 10px;
    font-weight: 800;
}
.s-tag-blk { background: #000; color: #fff; padding: 4px 8px; border-radius: 2px 0 0 2px; }
.s-tag-blu { background: #007bff; color: #fff; padding: 4px 8px; border-radius: 0 2px 2px 0; }

/* Stage & Products */
.shopee-stage {
    position: absolute;
    bottom: -20px;
    left: 0; width: 100%; height: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    z-index: 2;
}
.s-glow {
    position: absolute;
    bottom: 20px;
    width: 250px; height: 100px;
    background: radial-gradient(ellipse at center, rgba(255,100,0,0.8) 0%, rgba(255,0,0,0) 70%);
    z-index: 1;
}
.s-hero-img {
    position: relative;
    z-index: 2;
    max-height: 160px;
    max-width: 80%;
    object-fit: contain;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5));
}
.s-star {
    position: absolute;
    color: #fff;
    z-index: 3;
    animation: twinkle 1s infinite alternate;
}
.s1 { top: 40px; left: 60px; font-size: 20px; color: #ffcc00; }
.s2 { top: 60px; right: 80px; font-size: 16px; animation-delay: 0.3s; }
.s3 { bottom: 50px; right: 40px; font-size: 24px; animation-delay: 0.6s; color: #ffcc00; }
@keyframes twinkle {
    0% { transform: scale(0.8); opacity: 0.5; }
    100% { transform: scale(1.2); opacity: 1; }
}

/* BOTTOM SECTION */
.shopee-bot-sec {
    background: #111;
    border-radius: 0 0 12px 12px;
    padding: 20px 15px;
    position: relative;
    border: 2px dashed #d32f2f;
    border-top: none;
    z-index: 3;
    text-align: center;
}

.s-title {
    color: #fff;
    font-size: 18px;
    font-weight: 900;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.s-title-icon { font-size: 20px; }

/* Ribbon */
.s-ribbon-wrap {
    background: linear-gradient(90deg, #d32f2f, #ff5722);
    border: 1px solid #ffcc00;
    border-radius: 6px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    margin-bottom: 16px;
}
.s-percent {
    font-size: 32px;
    font-weight: 900;
    color: #fff;
    font-style: italic;
    margin-right: 12px;
    line-height: 1;
}
.s-ribbon-text {
    text-align: left;
}
.s-rb-small {
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.s-rb-large {
    color: #ffcc00;
    font-size: 14px;
    font-weight: 900;
    line-height: 1.1;
    text-shadow: 1px 1px 0 #000;
}

/* Vouchers */
.s-vouchers {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
}
.s-vou {
    flex: 1;
    border-radius: 6px;
    padding: 10px 0;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.s-vou::before, .s-vou::after {
    content: '';
    position: absolute;
    width: 10px; height: 10px;
    background: #111;
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
}
.s-vou::before { left: -5px; }
.s-vou::after { right: -5px; }

.v-pink { background: linear-gradient(135deg, #ffcce0, #ffa6c9); color: #d32f2f; }
.v-orange { background: linear-gradient(135deg, #ffe082, #ffb74d); color: #d32f2f; border: 1px solid #ffcc00; }
.v-blue { background: linear-gradient(135deg, #b2ebf2, #80deea); color: #00838f; }

.v-badge {
    position: absolute;
    top: -8px;
    background: #ff0000;
    color: #fff;
    font-size: 8px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 10px;
    border: 1px solid #fff;
    white-space: nowrap;
}

.v-sm { font-size: 9px; font-weight: 800; margin-bottom: 2px; }
.v-lg { font-size: 18px; font-weight: 900; line-height: 1; }

.s-tnc {
    font-size: 9px;
    color: #888;
    margin-bottom: 16px;
    line-height: 1.3;
}

/* Button */
.s-btn-wrap {
    margin-bottom: 12px;
}
.s-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: #000;
    text-decoration: none;
    font-weight: 900;
    font-size: 16px;
    padding: 12px 20px;
    border-radius: 25px;
    width: 100%;
}
.s-btn:hover {
    background: #f0f0f0;
    color: #000;
}
.s-btn-icon {
    color: #d32f2f;
    margin-left: 8px;
    font-size: 18px;
    display: flex;
    align-items: center;
}

.s-footer-tag {
    display: inline-block;
    background: #222;
    color: #ccc;
    font-size: 10px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 4px;
}

[x-cloak] { display: none !important; }
</style>
@endif
