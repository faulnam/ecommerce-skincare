@extends('layouts.admin')

@section('page-title', 'Edit Produk')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i>Edit Produk: {{ $product->name }}
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Produk</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3" placeholder="Deskripsi umum produk">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Specifications Section -->
                    <div class="card border-info mb-3" id="specificationsCard">
                        <div class="card-header bg-info bg-opacity-10">
                            <i class="fas fa-list me-1"></i>Spesifikasi Produk
                        </div>
                        <div class="card-body">
                            @php
                                $specFields = [
                                    ['name' => 'brand', 'label' => 'Brand', 'category' => 'Brand'],
                                    ['name' => 'series', 'label' => 'Series', 'category' => 'Series'],
                                    ['name' => 'shape', 'label' => 'Shape', 'category' => 'Shape'],
                                    ['name' => 'balance', 'label' => 'Balance', 'category' => 'Balance'],
                                    ['name' => 'hijab_weight', 'label' => 'Hijab Weight', 'category' => 'Hijab weight'],
                                    ['name' => 'play_style', 'label' => 'Play Style', 'category' => 'Play style'],
                                    ['name' => 'core', 'label' => 'Core', 'category' => 'Core'],
                                    ['name' => 'carbon_type', 'label' => 'Carbon (Faces)', 'category' => 'Carbon'],
                                    ['name' => 'surface', 'label' => 'Surface', 'category' => 'Surface'],
                                    ['name' => 'feel', 'label' => 'Feel', 'category' => 'Feel'],
                                    ['name' => 'power', 'label' => 'Power', 'category' => 'Power'],
                                    ['name' => 'control', 'label' => 'Control', 'category' => 'Control'],
                                    ['name' => 'maneuverability', 'label' => 'Maneuverability', 'category' => 'Maneuverability'],
                                    ['name' => 'comfort', 'label' => 'Comfort', 'category' => 'Comfort'],
                                ];
                            @endphp
                            
                            <div class="row">
                                @foreach($specFields as $field)
                                    <div class="col-md-6 mb-3">
                                        <label for="{{ $field['name'] }}" class="form-label">{{ $field['label'] }}</label>
                                        <select class="form-select @error($field['name']) is-invalid @enderror" id="{{ $field['name'] }}" name="{{ $field['name'] }}">
                                            <option value="">Pilih {{ $field['label'] }}...</option>
                                            @if(isset($specifications[$field['category']]))
                                                @foreach($specifications[$field['category']] as $spec)
                                                    <option value="{{ $spec->name }}" {{ old($field['name'], $product->{$field['name']}) == $spec->name ? 'selected' : '' }}>
                                                        {{ $spec->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error($field['name'])
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="priceAndStockRow">
                        <div class="col-md-6">
                            <div class="card border-primary mb-3 position-relative" id="mainProductPriceSection" style="border-radius: 6px; box-shadow: none;">
                                <div id="priceLockOverlay" class="position-absolute w-100 h-100 bg-white" style="opacity: 0.7; z-index: 10; display: none; border-radius: 6px;">
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100 text-center px-3" style="border: 2px dashed #0d6efd; border-radius: 6px;">
                                        <p class="text-primary-emphasis fw-bold mb-0">Harga utama dinonaktifkan karena produk memiliki varian. Harga produk akan menyesuaikan harga varian terendah.</p>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" min="0">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="mainStockSection">
                                <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                       id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                                       {{ $product->has_variants ? 'readonly' : 'required' }}>
                                @if($product->has_variants)
                                    <small class="text-muted">Stok dihitung otomatis dari varian.</small>
                                @endif
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Diskon Produk -->
                    <div class="card border-warning mb-3 position-relative" id="mainProductDiscountSection">
                        <div class="card-header bg-warning bg-opacity-10">
                            <i class="fas fa-percent me-1"></i>Diskon Produk (Opsional)
                            @if($product->hasActiveDiscount())
                                <span class="badge bg-success ms-2">Aktif</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="discount_percent" class="form-label">Diskon</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('discount_percent') is-invalid @enderror" 
                                                   id="discount_percent" name="discount_percent" value="{{ old('discount_percent', $product->discount_percent) }}" min="0" max="100" step="0.01">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('discount_percent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="discount_start" class="form-label">Mulai</label>
                                        <input type="datetime-local" class="form-control @error('discount_start') is-invalid @enderror" 
                                               id="discount_start" name="discount_start" value="{{ old('discount_start', $product->discount_start?->format('Y-m-d\TH:i')) }}">
                                        @error('discount_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="discount_end" class="form-label">Berakhir</label>
                                        <input type="datetime-local" class="form-control @error('discount_end') is-invalid @enderror" 
                                               id="discount_end" name="discount_end" value="{{ old('discount_end', $product->discount_end?->format('Y-m-d\TH:i')) }}">
                                        @error('discount_end')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @if($product->hasActiveDiscount())
                                <div class="alert alert-success mb-0">
                                    <small><strong>Harga setelah diskon:</strong> {{ $product->formatted_discounted_price }} <del class="text-muted">{{ $product->formatted_price }}</del></small>
                                </div>
                            @else
                                <small class="text-muted">Kosongkan tanggal jika diskon berlaku selamanya. Set diskon 0 untuk menonaktifkan. (Jika produk memiliki varian, tanggal akan diterapkan ke semua varian, sedangkan persentase diskon per varian diatur di bawah).</small>
                            @endif
                            <div id="discountedPricePreview" class="mt-2 text-sm text-emerald-700" style="display:none;"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori Produk <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach(\App\Models\Product::categories() as $value => $label)
                                        <option value="{{ $value }}" {{ old('category', $product->category) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="package_type" class="form-label">Tipe Paket</label>
                                <select class="form-select @error('package_type') is-invalid @enderror" id="package_type" name="package_type" onchange="toggleBundleType()">
                                    <option value="single" {{ old('package_type', $product->package_type ?? 'single') == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="bundle" {{ old('package_type', $product->package_type) == 'bundle' ? 'selected' : '' }}>Bundle</option>
                                </select>
                                @error('package_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Badge "Best Seller" akan muncul otomatis jika produk terjual lebih dari 3 kali.</small>
                            </div>

                            <div class="mb-3" id="bundleTypeContainer" style="display: {{ old('package_type', $product->package_type) == 'bundle' ? 'block' : 'none' }};">
                                <label for="bundle_type" class="form-label">Pilihan Bundling</label>
                                <select class="form-select @error('bundle_type') is-invalid @enderror" id="bundle_type" name="bundle_type">
                                    <option value="">Pilih Jenis Bundling</option>
                                    <option value="Starter Pack" {{ old('bundle_type', $product->bundle_type) == 'Starter Pack' ? 'selected' : '' }}>Starter Pack (Hijab entry, ball, grip)</option>
                                    <option value="Game Ready Set" {{ old('bundle_type', $product->bundle_type) == 'Game Ready Set' ? 'selected' : '' }}>Game Ready Set (Hijab mid, shoes) same brand</option>
                                    <option value="Maintenance Kit" {{ old('bundle_type', $product->bundle_type) == 'Maintenance Kit' ? 'selected' : '' }}>Maintenance Kit (Grip, edge protector, ball)</option>
                                    <option value="Brand Bundle" {{ old('bundle_type', $product->bundle_type) == 'Brand Bundle' ? 'selected' : '' }}>Brand Bundle (Hijab, bag, grip) (1 brand)</option>
                                    <option value="Gift Bundle" {{ old('bundle_type', $product->bundle_type) == 'Gift Bundle' ? 'selected' : '' }}>Gift Bundle (Hijab, ball, mini bag) with gift box</option>
                                </select>
                                @error('bundle_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="package_weight" class="form-label">Berat Paket (Gram)</label>
                                <input type="number" class="form-control @error('package_weight') is-invalid @enderror" id="package_weight" name="package_weight" value="{{ old('package_weight', $product->package_weight ?? 500) }}" min="0">
                                @error('package_weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="col-md-4" id="mainImageColumn">
                    <div class="mb-4 position-relative" id="mainProductImageSection">
                        <div id="imageLockOverlay" class="position-absolute w-100 h-100 bg-white" style="opacity: 0.7; z-index: 10; display: none;">
                            <div class="d-flex align-items-center justify-content-center h-100 w-100 text-center px-3" style="border: 2px dashed #ccc; border-radius: 8px;">
                                <p class="text-danger fw-bold mb-0">Gambar utama dinonaktifkan karena produk memiliki varian. Silakan upload gambar pada masing-masing varian.</p>
                            </div>
                        </div>
                        <label class="form-label fw-bold">Gambar Produk <span class="text-danger" id="mainImageRequiredMarker">*</span></label>
                        <small class="text-muted d-block mb-2">Upload minimal 1 gambar, maksimal 4 gambar. Gambar #1 akan menjadi gambar utama.</small>
                        
                        <!-- Image 1 (Main) -->
                        <div class="mb-3">
                            <label for="image" class="form-label text-primary fw-bold">Gambar #1 (Utama) <span class="text-danger" id="imageRequiredMarker">*</span></label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ $product->image_url }}" class="img-fluid rounded border" style="max-height: 120px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*" onchange="previewImage(this, 1)" data-has-image="{{ $product->image ? '1' : '0' }}">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar utama</small>
                            <div id="imagePreview1" class="mt-2"></div>
                        </div>
                        
                        <!-- Image 2 -->
                        <div class="mb-3">
                            <label for="image_2" class="form-label">Gambar #2 (Opsional)</label>
                            @if($product->image_2)
                                <div class="mb-2">
                                    <img src="{{ $product->image_2_url }}" class="img-fluid rounded border" style="max-height: 120px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image_2') is-invalid @enderror" 
                                   id="image_2" name="image_2" accept="image/*" onchange="previewImage(this, 2)">
                            @error('image_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                            <div id="imagePreview2" class="mt-2"></div>
                        </div>
                        
                        <!-- Image 3 -->
                        <div class="mb-3">
                            <label for="image_3" class="form-label">Gambar #3 (Opsional)</label>
                            @if($product->image_3)
                                <div class="mb-2">
                                    <img src="{{ $product->image_3_url }}" class="img-fluid rounded border" style="max-height: 120px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image_3') is-invalid @enderror" 
                                   id="image_3" name="image_3" accept="image/*" onchange="previewImage(this, 3)">
                            @error('image_3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                            <div id="imagePreview3" class="mt-2"></div>
                        </div>
                        
                        <!-- Image 4 -->
                        <div class="mb-3">
                            <label for="image_4" class="form-label">Gambar #4 (Opsional)</label>
                            @if($product->image_4)
                                <div class="mb-2">
                                    <img src="{{ $product->image_4_url }}" class="img-fluid rounded border" style="max-height: 120px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image_4') is-invalid @enderror" 
                                   id="image_4" name="image_4" accept="image/*" onchange="previewImage(this, 4)">
                            @error('image_4')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                            <div id="imagePreview4" class="mt-2"></div>
                        </div>
                        
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB per gambar</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktifkan Produk</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>

            <!-- Varian Produk -->
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="has_variants" name="has_variants" value="1"
                        {{ old('has_variants', $product->has_variants) ? 'checked' : '' }} onchange="toggleVariants(this)">
                    <label class="form-check-label fw-semibold" for="has_variants">
                        <i class="fas fa-layer-group me-1"></i>Produk ini memiliki Varian
                    </label>
                </div>
                <small class="text-muted">Aktifkan jika produk memiliki pilihan warna, ukuran, dll.</small>
            </div>

            <div id="variantsSection" style="display:{{ old('has_variants', $product->has_variants) ? 'block' : 'none' }}">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-layer-group me-1"></i>Daftar Varian</span>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addVariant()">
                            <i class="fas fa-plus me-1"></i>Tambah Varian
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="variantsList">
                            @foreach($product->variants as $vi => $variant)
                            <div class="border rounded p-3 mb-3 variant-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Varian #{{ $vi + 1 }}</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariant(this)"><i class="fas fa-trash"></i></button>
                                </div>
                                <input type="hidden" name="variants[{{ $vi }}][id]" value="{{ $variant->id }}">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Nama Varian <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="variants[{{ $vi }}][name]" value="{{ $variant->name }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Stok <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="variants[{{ $vi }}][stock]" value="{{ $variant->stock }}" min="0" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Harga Varian <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" name="variants[{{ $vi }}][price]" value="{{ $variant->price }}" required min="0" step="100" oninput="updateVariantPreview(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Diskon Varian (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control variant-discount-input" name="variants[{{ $vi }}][discount_percent]" value="{{ $variant->discount_percent }}" min="0" max="100" step="0.1" oninput="updateVariantPreview(this)">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mt-1">
                                    <div class="col-md-7"></div>
                                    <div class="col-md-5">
                                        <div class="variant-preview text-success fw-bold small" style="{{ $variant->discount_percent > 0 ? 'display:block;' : 'display:none;' }}">
                                            @if($variant->discount_percent > 0)
                                                Harga setelah diskon: Rp {{ number_format($variant->price - ($variant->price * ($variant->discount_percent / 100)), 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Gambar Utama</label>
                                        @if($variant->image)
                                            <div class="mb-1"><img src="{{ $variant->image_url }}" style="height: 50px;" class="rounded"></div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="variants[{{ $vi }}][image]" accept="image/*">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Angle 2</label>
                                        @if($variant->image_2)
                                            <div class="mb-1"><img src="{{ $variant->image_2_url }}" style="height: 50px;" class="rounded"></div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="variants[{{ $vi }}][image_2]" accept="image/*">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Angle 3</label>
                                        @if($variant->image_3)
                                            <div class="mb-1"><img src="{{ $variant->image_3_url }}" style="height: 50px;" class="rounded"></div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="variants[{{ $vi }}][image_3]" accept="image/*">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Angle 4</label>
                                        @if($variant->image_4)
                                            <div class="mb-1"><img src="{{ $variant->image_4_url }}" style="height: 50px;" class="rounded"></div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="variants[{{ $vi }}][image_4]" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <p id="variantsEmpty" style="display:{{ $product->variants->count() ? 'none' : 'block' }}" class="text-muted text-center py-3">Belum ada varian.</p>
                    </div>
                </div>
            </div>

            <hr>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input, imageNumber) {
    const preview = document.getElementById('imagePreview' + imageNumber);
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded border" style="max-height:120px;">`;
        reader.readAsDataURL(input.files[0]);
    }
}

let variantCount = {{ $product->variants->count() }};

function toggleVariants(cb) {
    const section = document.getElementById('variantsSection');
    const stockInput = document.getElementById('stock');
    const imageInput = document.getElementById('image');
    const imageLockOverlay = document.getElementById('imageLockOverlay');
    const mainImageRequiredMarker = document.getElementById('mainImageRequiredMarker');
    const imageRequiredMarker = document.getElementById('imageRequiredMarker');
    const priceLockOverlay = document.getElementById('priceLockOverlay');
    const priceInput = document.getElementById('price');
    const priceAndStockRow = document.getElementById('priceAndStockRow');
    const mainImageColumn = document.getElementById('mainImageColumn');
    const discountPercent = document.getElementById('discount_percent');

    section.style.display = cb.checked ? 'block' : 'none';
    stockInput.readOnly = cb.checked;
    
    if (cb.checked) {
        stockInput.removeAttribute('required');
        priceInput.removeAttribute('required');
        
        if (priceAndStockRow) priceAndStockRow.style.display = 'none';
        if (mainImageColumn) mainImageColumn.style.display = 'none';
        if (discountPercent) discountPercent.readOnly = true;
    } else {
        stockInput.setAttribute('required', '');
        priceInput.setAttribute('required', '');
        
        if (priceAndStockRow) priceAndStockRow.style.display = 'flex';
        if (mainImageColumn) mainImageColumn.style.display = 'block';
        if (discountPercent) discountPercent.readOnly = false;
    }
    toggleRequirementFields();
}

function toggleRequirementFields() {
    const isFeatured = document.getElementById('is_featured')?.checked;
    const hasVariants = document.getElementById('has_variants')?.checked;
    const skipRequired = isFeatured || hasVariants;
    const requiredFields = ['name', 'description', 'price', 'stock', 'category', 'weight'];
    requiredFields.forEach((id) => {
        const field = document.getElementById(id);
        if (!field) return;
        if (id === 'stock' && hasVariants) {
            field.removeAttribute('required');
            return;
        }
        if (skipRequired) {
            field.removeAttribute('required');
        } else {
            field.setAttribute('required', 'required');
        }
    });

    const imageInput = document.getElementById('image');
    if (imageInput) {
        const hasImage = imageInput.dataset.hasImage === '1';
        if (hasVariants) {
            imageInput.removeAttribute('required');
        } else if (isFeatured && !hasImage) {
            imageInput.setAttribute('required', 'required');
        } else if (!isFeatured && !hasImage) {
            imageInput.setAttribute('required', 'required');
        } else {
            imageInput.removeAttribute('required');
        }
    }
}

function addVariant(data = {}) {
    const i = variantCount++;
    const list = document.getElementById('variantsList');
    document.getElementById('variantsEmpty').style.display = 'none';
    const div = document.createElement('div');
    div.className = 'border rounded p-3 mb-3 variant-item';
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Varian Baru</strong>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariant(this)"><i class="fas fa-trash"></i></button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Nama Varian <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="variants[${i}][name]" placeholder="cth: Merah, XL" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stok <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="variants[${i}][stock]" min="0" value="0" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Harga Varian <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" name="variants[${i}][price]" value="${data.price !== undefined ? data.price : ''}" required min="0" step="100" oninput="updateVariantPreview(this)">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Diskon Varian (%)</label>
                <div class="input-group">
                    <input type="number" class="form-control variant-discount-input" name="variants[${i}][discount_percent]" value="0" min="0" max="100" step="0.1" oninput="updateVariantPreview(this)">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>
        <div class="row g-2 mt-1">
            <div class="col-md-7"></div>
            <div class="col-md-5">
                <div class="variant-preview text-success fw-bold small" style="display:none;"></div>
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-3">
                <label class="form-label">Gambar Utama</label>
                <input type="file" class="form-control form-control-sm" name="variants[${i}][image]" accept="image/*">
            </div>
            <div class="col-md-3">
                <label class="form-label">Angle 2</label>
                <input type="file" class="form-control form-control-sm" name="variants[${i}][image_2]" accept="image/*">
            </div>
            <div class="col-md-3">
                <label class="form-label">Angle 3</label>
                <input type="file" class="form-control form-control-sm" name="variants[${i}][image_3]" accept="image/*">
            </div>
            <div class="col-md-3">
                <label class="form-label">Angle 4</label>
                <input type="file" class="form-control form-control-sm" name="variants[${i}][image_4]" accept="image/*">
            </div>
        </div>`;
    list.appendChild(div);
}

function removeVariant(btn) {
    btn.closest('.variant-item').remove();
    if (!document.querySelectorAll('.variant-item').length) {
        document.getElementById('variantsEmpty').style.display = 'block';
    }
}

function formatRupiah(value) {
    return 'Rp ' + Math.round(value).toLocaleString('id-ID');
}

function setupDiscountAutoApply() {
    const priceInput = document.getElementById('price');
    const discountInput = document.getElementById('discount_percent');
    const preview = document.getElementById('discountedPricePreview');
    const form = priceInput?.closest('form');

    if (!priceInput || !discountInput || !form || !preview) {
        return;
    }

    const getBasePrice = () => {
        let val = priceInput.value;
        if (!val) return 0;
        return parseFloat(val);
    };

    const applyDiscount = () => {
        const discount = parseFloat(discountInput.value || '0');
        const basePrice = getBasePrice();

        if (!basePrice || isNaN(basePrice) || discount <= 0) {
            preview.style.display = 'none';
            preview.textContent = '';
            return;
        }

        const discounted = basePrice - (basePrice * (discount / 100));
        preview.style.display = 'block';
        preview.textContent = `Harga setelah diskon: ${formatRupiah(discounted)}`;
    };

    priceInput.addEventListener('input', applyDiscount);
    discountInput.addEventListener('input', applyDiscount);
    
    // Initial call
    applyDiscount();
}

function toggleBundleType() {
    const packageType = document.getElementById('package_type').value;
    const container = document.getElementById('bundleTypeContainer');
    if (packageType === 'bundle') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
        document.getElementById('bundle_type').value = '';
    }
}

function updateVariantPreview(input) {
    const row = input.closest('.variant-item');
    const priceInput = row.querySelector('input[name*="[price]"]');
    const discountInput = row.querySelector('.variant-discount-input');
    const preview = row.querySelector('.variant-preview');
    
    if (!priceInput || !discountInput || !preview) return;
    
    const basePrice = parseFloat(priceInput.value || '0');
    const discount = parseFloat(discountInput.value || '0');
    
    if (!basePrice || isNaN(basePrice) || discount <= 0) {
        preview.style.display = 'none';
        preview.textContent = '';
        return;
    }
    
    const discounted = basePrice - (basePrice * (discount / 100));
    preview.style.display = 'block';
    preview.textContent = `Harga setelah diskon: Rp ${Math.round(discounted).toLocaleString('id-ID')}`;
}

document.addEventListener('DOMContentLoaded', () => {
    setupDiscountAutoApply();

    // Category toggle for specifications
    const categorySelect = document.getElementById('category');
    const specCard = document.getElementById('specificationsCard');
    
    function toggleSpecCard() {
        if (categorySelect && specCard) {
            specCard.style.display = categorySelect.value === 'hijab' ? 'block' : 'none';
        }
    }
    
    if (categorySelect) {
        categorySelect.addEventListener('change', toggleSpecCard);
        toggleSpecCard(); // Initialize on page load
    }

    const featuredCheckbox = document.getElementById('is_featured');
    if (featuredCheckbox) {
        featuredCheckbox.addEventListener('change', toggleRequirementFields);
    }
    const variantsCheckbox = document.getElementById('has_variants');
    if (variantsCheckbox) {
        toggleVariants(variantsCheckbox);
    } else {
        toggleRequirementFields();
    }

    // Initialize discount preview for existing variants
    document.querySelectorAll('.variant-discount-input').forEach(input => {
        updateVariantPreview(input);
    });
});
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
// Initialize CKEditor for description
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('#description') && typeof ClassicEditor !== 'undefined') {
        ClassicEditor
            .create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
            })
            .catch(error => {
                console.error(error);
            });
    }
});
</script>
<style>
    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>
@endpush
@endsection
