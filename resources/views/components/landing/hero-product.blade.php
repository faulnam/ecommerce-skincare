@props([
    'id' => null,
    'title',
    'subtitle',
    'image',
    'alt',
    'primaryText' => 'Explore',
    'primaryHref' => '#',
    'secondaryText' => 'Buy Now',
    'secondaryHref' => '#',
    'sectionClass' => 'bg-[#f5f5f7]',
    'contentClass' => '',
    'imageWrapperClass' => '',
    'ctaClass' => '',
])

<section id="{{ $id }}" class="np-fade-section relative {{ $sectionClass }} py-12 md:py-16" data-scroll-container>
    <div class="w-full relative" style="perspective: 1000px; padding: 1rem 1.5rem;">
        <div class="np-container-scroll-content mx-auto w-full max-w-5xl text-center {{ $contentClass }}">
            <h2 class="text-4xl font-semibold tracking-tight text-black sm:text-5xl lg:text-6xl">{{ $title }}</h2>
            <p class="mx-auto mt-1 max-w-2xl text-lg font-normal text-zinc-700 sm:text-2xl">{{ $subtitle }}</p>
        </div>

        <div class="np-container-scroll-card relative mx-auto mt-8 max-w-5xl {{ $imageWrapperClass }}" style="transform-style: preserve-3d; box-shadow: 0 0 #0000004d, 0 9px 20px #0000004a, 0 37px 37px #00000042, 0 84px 50px #00000026, 0 149px 60px #0000000a, 0 233px 65px #00000003;">
            <img
                src="{{ $image }}"
                alt="{{ $alt }}"
                class="mx-auto h-auto w-full max-h-[400px] rounded-3xl object-contain"
                loading="lazy"
            >
        </div>
    </div>
</section>