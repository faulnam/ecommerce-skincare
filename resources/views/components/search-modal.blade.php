<div id="searchModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
    <div class="relative mx-4 w-full max-w-2xl">
        <div class="rounded-3xl bg-white shadow-2xl">
            <div class="border-b border-zinc-200 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-search text-zinc-400"></i>
                    <input 
                        type="text" 
                        id="globalSearchInput" 
                        placeholder="Cari produk..." 
                        class="w-full border-0 bg-transparent text-base outline-none focus:ring-0"
                        autocomplete="off"
                    >
                    <button type="button" onclick="closeSearchModal()" class="flex h-8 w-8 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div id="searchResults" class="max-h-[60vh] overflow-y-auto p-4">
                <div class="text-center text-sm text-zinc-500">
                    Ketik untuk mencari produk...
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #searchModal.show {
        display: flex !important;
    }
</style>

<script>
    let searchTimeout;
    
    function openSearchModal() {
        const modal = document.getElementById('searchModal');
        const input = document.getElementById('globalSearchInput');
        modal.classList.add('show');
        setTimeout(() => input.focus(), 100);
    }
    
    function closeSearchModal() {
        const modal = document.getElementById('searchModal');
        const input = document.getElementById('globalSearchInput');
        modal.classList.remove('show');
        input.value = '';
        document.getElementById('searchResults').innerHTML = '<div class="text-center text-sm text-zinc-500">Ketik untuk mencari produk...</div>';
    }
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSearchModal();
        }
    });
    
    // Close on backdrop click
    document.getElementById('searchModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeSearchModal();
        }
    });
    
    // Search functionality
    document.getElementById('globalSearchInput')?.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        const resultsDiv = document.getElementById('searchResults');
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            resultsDiv.innerHTML = '<div class="text-center text-sm text-zinc-500">Ketik minimal 2 karakter...</div>';
            return;
        }
        
        resultsDiv.innerHTML = '<div class="text-center text-sm text-zinc-500"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari...</div>';
        
        searchTimeout = setTimeout(() => {
            fetch(`/api/search-products?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.products && data.products.length > 0) {
                        let html = '<div class="space-y-2">';
                        data.products.forEach(product => {
                            html += `
                                <a href="${'/produk/' + product.slug}" class="flex items-center gap-3 rounded-xl border border-zinc-200 p-3 transition hover:border-zinc-300 hover:bg-zinc-50" onclick="closeSearchModal()">
                                    <img src="${product.image_url}" alt="${product.name}" class="h-16 w-16 rounded-lg object-cover" onerror="this.src='/images/logo.png'">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-black line-clamp-1">${product.name}</h4>
                                        <p class="text-xs text-zinc-500">${product.category_label}</p>
                                        <p class="mt-1 font-bold text-black">${product.formatted_price}</p>
                                    </div>
                                </a>
                            `;
                        });
                        html += '</div>';
                        resultsDiv.innerHTML = html;
                    } else {
                        resultsDiv.innerHTML = '<div class="text-center text-sm text-zinc-500">Tidak ada produk ditemukan</div>';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    resultsDiv.innerHTML = '<div class="text-center text-sm text-red-500">Terjadi kesalahan saat mencari</div>';
                });
        }, 300);
    });
</script>
