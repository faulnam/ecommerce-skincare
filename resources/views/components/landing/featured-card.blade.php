@props([
    'title',
    'subtitle',
    'image',
    'href' => '#',
])

<a href="{{ $href }}" class="group block rounded-2xl border border-black/5 bg-white p-8 text-center shadow-[0_6px_24px_rgba(0,0,0,0.04)] transition duration-300 hover:scale-[1.015] hover:shadow-[0_10px_34px_rgba(0,0,0,0.08)]">
    <img src="{{ $image }}" alt="{{ $title }}" class="mx-auto h-44 w-auto object-contain" loading="lazy">
    <h3 class="mt-6 text-2xl font-semibold tracking-tight text-black">{{ $title }}</h3>
    <p class="mt-2 text-sm text-zinc-600">{{ $subtitle }}</p>
</a>