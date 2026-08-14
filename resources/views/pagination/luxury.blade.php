@if ($paginator->hasPages())
    @php
        $lang = 'id';
        $translations = [
            'en' => ['previous' => 'Back', 'next' => 'Next', 'jump_title' => 'Jump to Page', 'cancel' => 'Cancel', 'go' => 'Go', 'page' => 'Page'],
            'id' => ['previous' => 'Kembali', 'next' => 'Lanjut', 'jump_title' => 'Lompat ke Halaman', 'cancel' => 'Batal', 'go' => 'Pergi', 'page' => 'Halaman']
        ];
        $prevText = $translations[$lang]['previous'];
        $nextText = $translations[$lang]['next'];
        $t = $translations[$lang];
    @endphp

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Large variation for Home page */
        .pagination-large .page-btn { width: 3rem; height: 3rem; font-size: 1rem; }
        @media (min-width: 768px) {
            .pagination-large .page-btn { width: 3.5rem; height: 3.5rem; font-size: 1.125rem; }
            .pagination-large nav { gap: 0.75rem; }
        }
    </style>

    <div class="w-full py-6 px-2">

        <nav class="hidden md:flex items-center justify-center gap-2 flex-wrap w-full">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-btn w-12 h-12 flex items-center justify-center text-base font-semibold text-zinc-300 cursor-not-allowed border border-zinc-100 rounded-lg transition-all duration-200">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-btn w-12 h-12 flex items-center justify-center text-base font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 hover:border-zinc-300 hover:text-black transition-all duration-200 shadow-sm border-shadow">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-btn w-12 h-12 flex items-center justify-center text-base font-medium text-zinc-400 border border-zinc-200 rounded-lg cursor-default bg-zinc-50/50">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-btn w-12 h-12 flex items-center justify-center text-base font-bold bg-black text-white rounded-lg border border-black shadow-md z-10">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="page-btn w-12 h-12 flex items-center justify-center text-base font-medium text-zinc-600 bg-white border border-zinc-200 rounded-lg hover:border-zinc-800 hover:text-black hover:bg-zinc-50 transition-all duration-200 shadow-sm border-shadow">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-btn w-12 h-12 flex items-center justify-center text-base font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 hover:border-zinc-300 hover:text-black transition-all duration-200 shadow-sm border-shadow">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-btn w-12 h-12 flex items-center justify-center text-base font-semibold text-zinc-300 cursor-not-allowed border border-zinc-100 rounded-lg transition-all duration-200">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </nav>

        <nav class="flex md:hidden items-center justify-center gap-1.5 w-full">
            {{-- Prev Arrow --}}
            @if ($paginator->onFirstPage())
                <span class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-zinc-300 cursor-not-allowed border border-zinc-100 rounded-lg">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-all shadow-sm">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @if($paginator->lastPage() <= 4)
                {{-- If 4 pages or fewer, just show them all to avoid unnecessary modal --}}
                @foreach(range(1, $paginator->lastPage()) as $page)
                    <a href="{{ $paginator->url($page) }}" class="w-10 h-10 flex items-center justify-center text-sm font-semibold rounded-lg shadow-sm {{ $page == $paginator->currentPage() ? 'bg-black text-white border-black' : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50' }}">
                        {{ $page }}
                    </a>
                @endforeach
            @else
                {{-- Page 1 --}}
                <a href="{{ $paginator->url(1) }}" class="w-10 h-10 flex items-center justify-center text-sm font-semibold rounded-lg shadow-sm {{ $paginator->currentPage() == 1 ? 'bg-black text-white border-black' : 'bg-white text-zinc-600 border-zinc-200' }}">
                    1
                </a>

                {{-- Dynamic Page 2 (Shows current page if user is deep in pagination) --}}
                @php
                    $middlePage = 2;
                    if ($paginator->currentPage() > 2 && $paginator->currentPage() < $paginator->lastPage()) {
                        $middlePage = $paginator->currentPage();
                    } elseif ($paginator->currentPage() == $paginator->lastPage()) {
                        $middlePage = $paginator->lastPage() - 1;
                    }
                @endphp
                <a href="{{ $paginator->url($middlePage) }}" class="w-10 h-10 flex items-center justify-center text-sm font-semibold rounded-lg shadow-sm {{ $paginator->currentPage() == $middlePage ? 'bg-black text-white border-black' : 'bg-white text-zinc-600 border-zinc-200' }}">
                    {{ $middlePage }}
                </a>

                {{-- Interactive Ellipsis (...) --}}
                <button type="button" onclick="openMobilePaginationModal()" class="w-10 h-10 flex items-center justify-center text-sm font-bold text-zinc-500 bg-zinc-50 border border-zinc-200 rounded-lg hover:bg-zinc-100 transition-all shadow-sm active:scale-95">
                    ...
                </button>

                {{-- Last Page --}}
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="w-10 h-10 flex items-center justify-center text-sm font-semibold rounded-lg shadow-sm {{ $paginator->currentPage() == $paginator->lastPage() ? 'bg-black text-white border-black' : 'bg-white text-zinc-600 border-zinc-200' }}">
                    {{ $paginator->lastPage() }}
                </a>
            @endif

            {{-- Next Arrow --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-all shadow-sm">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-zinc-300 cursor-not-allowed border border-zinc-100 rounded-lg">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </nav>
    </div>

    <div id="mobilePaginationModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="closeMobilePaginationModal()">
        <div class="bg-white rounded-2xl p-6 w-[85%] max-w-sm shadow-2xl transform scale-95 transition-transform duration-300" id="mobilePaginationPanel" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold text-black mb-4 text-center">{{ $t['jump_title'] }}</h3>

            <form action="{{ url()->current() }}" method="GET" class="flex flex-col gap-5">
                {{-- Preserve existing query parameters (like filters/sorting) --}}
                @foreach(request()->query() as $key => $value)
                    @if($key !== 'page' && !is_array($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @elseif(is_array($value))
                        @foreach($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @endif
                @endforeach

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 mb-2 uppercase tracking-wider">{{ $t['page'] }} (1 - {{ $paginator->lastPage() }})</label>
                    <input type="number" name="page" min="1" max="{{ $paginator->lastPage() }}" value="{{ $paginator->currentPage() }}"
                           class="w-full rounded-xl border border-zinc-300 px-4 py-3 text-lg font-bold text-center text-black focus:border-black focus:ring-2 focus:ring-black/20 outline-none transition-all" required>
                </div>

                <div class="flex gap-3 mt-2">
                    <button type="button" onclick="closeMobilePaginationModal()" class="flex-1 py-3 rounded-xl border border-zinc-200 bg-white text-zinc-700 font-semibold text-sm hover:bg-zinc-50 transition-colors active:scale-95">{{ $t['cancel'] }}</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white font-semibold text-sm hover:bg-zinc-800 transition-colors active:scale-95">{{ $t['go'] }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        if (typeof window.openMobilePaginationModal !== 'function') {

            let _paginationScrollY = 0;

            window.openMobilePaginationModal = function() {
                const modal = document.getElementById('mobilePaginationModal');
                const panel = document.getElementById('mobilePaginationPanel');

                // THE FIX: Move the modal directly to the <body> to escape broken CSS parents
                if (!modal._originalParent) {
                    modal._originalParent = modal.parentNode;
                }
                if (modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }

                // Lock body scroll to prevent background moving while popup is open
                _paginationScrollY = window.scrollY || window.pageYOffset;
                document.body.style.position = 'fixed';
                document.body.style.top = `-${_paginationScrollY}px`;
                document.body.style.width = '100%';

                modal.classList.remove('hidden');

                // Animation frame for smooth fade/scale in
                requestAnimationFrame(() => {
                    modal.classList.remove('opacity-0');
                    panel.classList.remove('scale-95');
                    panel.classList.add('scale-100');
                });
            };

            window.closeMobilePaginationModal = function() {
                const modal = document.getElementById('mobilePaginationModal');
                const panel = document.getElementById('mobilePaginationPanel');

                modal.classList.add('opacity-0');
                panel.classList.remove('scale-100');
                panel.classList.add('scale-95');

                setTimeout(() => {
                    modal.classList.add('hidden');

                    // Unlock body scroll and snap user back to where they were
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, _paginationScrollY);

                    // Optional: Put the modal back where it originally came from
                    if (modal._originalParent && modal.parentNode !== modal._originalParent) {
                        modal._originalParent.appendChild(modal);
                    }
                }, 300);
            };
        }
    </script>
@endif
