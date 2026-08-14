<!-- Filter Section -->
<div class="mb-6 space-y-4">
    <div class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" id="searchProduct" placeholder="Cari produk..." class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition">
        </div>
        <div class="flex gap-2 flex-wrap">
            <select id="filterBrand" class="px-4 py-2.5 border border-zinc-300 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition">
                <option value="">Semua Brand</option>
                @php
                    $brands = \App\Models\Product::whereNotNull('brand')->distinct()->pluck('brand')->sort();
                @endphp
                @foreach($brands as $brand)
                    <option value="{{ $brand }}">{{ $brand }}</option>
                @endforeach
            </select>
            <button id="filterPrice" class="px-4 py-2.5 border border-zinc-300 rounded-xl text-sm hover:bg-zinc-50 transition">
                <i class="fas fa-sliders-h mr-2"></i>Harga
            </button>
        </div>
    </div>

    <!-- Price Range Filter -->
    <div id="priceRangeFilter" class="hidden bg-zinc-50 border border-zinc-200 rounded-xl p-4">
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <label class="text-xs text-zinc-600 mb-1 block">Harga Min</label>
                <input type="number" id="minPrice" placeholder="0" class="w-full px-3 py-2 border border-zinc-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="text-xs text-zinc-600 mb-1 block">Harga Max</label>
                <input type="number" id="maxPrice" placeholder="999999999" class="w-full px-3 py-2 border border-zinc-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </div>
        <div class="flex gap-2">
            <button id="applyPriceFilter" class="flex-1 bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                Terapkan
            </button>
            <button id="resetPriceFilter" class="px-4 py-2 border border-zinc-300 rounded-lg text-sm hover:bg-white transition">
                Reset
            </button>
        </div>
    </div>
</div>

<script>
// Filter functionality
const searchInput = document.getElementById('searchProduct');
const filterBrand = document.getElementById('filterBrand');
const filterPriceBtn = document.getElementById('filterPrice');
const priceRangeFilter = document.getElementById('priceRangeFilter');
const minPriceInput = document.getElementById('minPrice');
const maxPriceInput = document.getElementById('maxPrice');
const applyPriceBtn = document.getElementById('applyPriceFilter');
const resetPriceBtn = document.getElementById('resetPriceFilter');
const productGrid = document.getElementById('productGrid');
const noResults = document.getElementById('noResults');
const products = document.querySelectorAll('.product-item');

let minPrice = 0;
let maxPrice = Infinity;

filterPriceBtn?.addEventListener('click', () => {
    priceRangeFilter.classList.toggle('hidden');
});

function filterProducts() {
    const searchTerm = searchInput.value.toLowerCase();
    const brandFilter = filterBrand.value.toLowerCase();
    let visibleCount = 0;

    products.forEach(product => {
        const name = product.dataset.name;
        const price = parseFloat(product.dataset.price);
        const brand = product.dataset.brand;

        let show = true;

        if (searchTerm && !name.includes(searchTerm)) show = false;
        if (brandFilter && brand !== brandFilter) show = false;
        if (price < minPrice || price > maxPrice) show = false;

        if (show) {
            product.style.display = '';
            visibleCount++;
        } else {
            product.style.display = 'none';
        }
    });

    if (visibleCount === 0) {
        productGrid.classList.add('hidden');
        noResults.classList.remove('hidden');
    } else {
        productGrid.classList.remove('hidden');
        noResults.classList.add('hidden');
    }
}

searchInput?.addEventListener('input', filterProducts);
filterBrand?.addEventListener('change', filterProducts);

applyPriceBtn?.addEventListener('click', () => {
    minPrice = parseFloat(minPriceInput.value) || 0;
    maxPrice = parseFloat(maxPriceInput.value) || Infinity;
    filterProducts();
});

resetPriceBtn?.addEventListener('click', () => {
    minPriceInput.value = '';
    maxPriceInput.value = '';
    minPrice = 0;
    maxPrice = Infinity;
    filterProducts();
});
</script>
