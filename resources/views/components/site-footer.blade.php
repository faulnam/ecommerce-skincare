@php
    $lang = 'id';
    $common = $common ?? [];
@endphp

<footer class="border-t border-black/10 bg-white py-12 text-zinc-600" data-parallax data-parallax-speed="0.01">
    <div class="mx-auto w-full max-w-7xl px-6 md:px-10 lg:px-12">
        
        <!-- Desktop Layout (Grid) -->
        <div class="hidden grid-cols-1 gap-10 sm:grid-cols-2 lg:grid md:grid-cols-4">
            <!-- Shop Section -->
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['shop'][$lang] ?? 'Belanja' }}
                </h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                   <li><a href="{{ route('brand-catalog') }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['brand_catalog'][$lang] ?? 'Katalog Brand' }}</a></li>
                    <li><a href="{{ route('new-arrivals') }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['new-arrivals'][$lang] ?? 'Produk Terbaru' }}</a></li>
                    <li><a href="{{ route('about') }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['about'][$lang] ?? 'Tentang Kami' }}</a></li>
                </ul>
            </div>

            <!-- Support Section -->
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['support'][$lang] ?? 'Bantuan' }}
                </h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('policy', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['policy'][$lang] ?? 'Kebijakan Privasi' }}</a></li>
                    <li><a href="{{ route('return-refund', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['return_refund'][$lang] ?? 'Pengembalian & Refund' }}</a></li>
                    <li><a href="{{ route('guarantee', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['guarantee'][$lang] ?? 'Garansi' }}</a></li>
                    <li><a href="{{ route('help-center', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['help_center'][$lang] ?? 'Pusat Bantuan' }}</a></li>
                </ul>
            </div>

            <!-- Account Section -->
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['account'][$lang] ?? 'Akun' }}
                </h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('customer.orders.lookup', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">Cek Pesanan</a></li>
                    <li><a href="{{ route('login', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['navbar']['login'][$lang] ?? 'Masuk' }}</a></li>
                    <li><a href="{{ route('register', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['register'][$lang] ?? 'Daftar' }}</a></li>
                </ul>
            </div>

            <!-- Social Media Section -->
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['social_media'][$lang] ?? 'Media Sosial' }}
                </h3>
                <div class="mt-4 flex gap-3">
                    <a href="https://www.instagram.com/skincare/" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@skincare" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://shopee.co.id/skincareds" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fas fa-shopping-bag"></i>
                    </a>
                    <a href="https://wa.me/6285117358568" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Layout (Accordions) -->
        <div class="space-y-2 md:hidden">
            <!-- Shop Accordion -->
            <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['shop'][$lang] ?? 'Belanja' }}
                    <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                </summary>
                <ul class="mt-3 space-y-2.5 text-sm">
                    <li><a href="{{ route('brand-catalog', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">Katalog Brand</a></li>
                    <li><a href="{{ route('new-arrivals', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">Produk Terbaru</a></li>
                    <li><a href="{{ route('about', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">Tentang Kami</a></li>
                </ul>
            </details>

            <!-- Support Accordion -->
            <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['support'][$lang] ?? 'Bantuan' }}
                    <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                </summary>
                <ul class="mt-3 space-y-2.5 text-sm">
                    <li><a href="{{ route('policy', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['policy'][$lang] ?? 'Kebijakan Privasi' }}</a></li>
                    <li><a href="{{ route('return-refund', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['return_refund'][$lang] ?? 'Pengembalian & Refund' }}</a></li>
                    <li><a href="{{ route('guarantee', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['guarantee'][$lang] ?? 'Garansi' }}</a></li>
                    <li><a href="{{ route('help-center', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['help_center'][$lang] ?? 'Pusat Bantuan' }}</a></li>
                </ul>
            </details>

            <!-- Account Accordion -->
            <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['account'][$lang] ?? 'Akun' }}
                    <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                </summary>
                <ul class="mt-3 space-y-2.5 text-sm">
                    <li><a href="{{ route('customer.orders.lookup', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">Cek Pesanan</a></li>
                    <li><a href="{{ route('login', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['navbar']['login'][$lang] ?? 'Masuk' }}</a></li>
                    <li><a href="{{ route('register', ['locale' => $lang]) }}" class="inline-flex transition-colors duration-200 hover:text-black">{{ $common['footer']['links']['register'][$lang] ?? 'Daftar' }}</a></li>
                </ul>
            </details>

            <!-- Social Media Accordion -->
            <details class="group rounded-xl border border-black/10 bg-white px-4 py-3">
                <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-black">
                    {{ $common['footer']['sections']['social_media'][$lang] ?? 'Media Sosial' }}
                    <i class="fas fa-chevron-down text-[10px] text-zinc-500 transition group-open:rotate-180"></i>
                </summary>
                <div class="mt-3 flex gap-3">
                    <a href="https://www.instagram.com/skincare/" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@skincare" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://shopee.co.id/skincareds" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fas fa-shopping-bag"></i>
                    </a>
                    <a href="https://wa.me/6285117358568" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-black transition-all duration-200 hover:bg-zinc-200">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </details>
        </div>

        <!-- Payment Gateway Badges -->
        <div class="mt-10 flex flex-wrap justify-center items-center gap-x-4 gap-y-3">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-4 w-auto object-contain transition-all duration-200" loading="lazy">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-4 w-auto object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/seabank.png' }}" alt="SeaBank" class="h-14 w-14 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/danamon.png' }}" alt="Danamon" class="h-14 w-14 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/permata.jpg' }}" alt="Permata" class="h-14 w-14 object-contain transition-all duration-200" loading="lazy">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" class="h-4 w-auto object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/bri.png' }}" alt="BRI" class="h-8 w-8 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/bni.png' }}" alt="BNI" class="h-8 w-8 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/btn.jpeg' }}" alt="BTN" class="h-10 w-10 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/cimb.png' }}" alt="CIMB" class="h-10 w-10 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/ocbc.png' }}" alt="OCBC" class="h-10 w-10 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/mega.png' }}" alt="Bank Mega" class="h-10 w-10 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/dana.jpg' }}" alt="DANA" class="h-10 w-10 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/gopay.webp' }}" alt="GoPay" class="h-12 w-12 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/shopeepay.png' }}" alt="ShopeePay" class="h-14 w-14 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/ovo.png' }}" alt="OVO" class="h-10 w-10 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/linkaja.webp' }}" alt="LinkAja" class="h-16 w-16 object-contain transition-all duration-200" loading="lazy">
            <img src="{{ config('filesystems.disks.r2.url').'/nobu.png' }}" alt="Nobu Bank" class="h-10 w-10 object-contain transition-all duration-200" loading="lazy">
        </div>

        <!-- Copyright Section -->
        <div class="mt-6 border-t border-black/10 pt-4 text-center text-sm text-zinc-500">
            {{ $common['footer']['copyright'][$lang] ?? ('© ' . now()->year . ' LUMINA. Hak cipta dilindungi.') }}
        </div>
    </div>
</footer>