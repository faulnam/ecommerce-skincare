@extends('layouts.admin')

@section('page-title', 'Edit Voucher')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i>Edit Voucher: {{ $voucher->title }}
    </div>
    <div class="card-body">
        <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Voucher <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $voucher->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $voucher->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode Voucher</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $voucher->code) }}">
                            <button type="button" onclick="generateCode()" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Voucher <span class="text-danger">*</span></label>
                        <select name="type" id="voucherType" class="form-select @error('type') is-invalid @enderror" required onchange="toggleDiscountFields()">
                            <option value="">Pilih Tipe</option>
                            <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>Diskon Tetap</option>
                            <option value="percent" {{ old('type', $voucher->type) === 'percent' ? 'selected' : '' }}>Diskon Persentase</option>
                            <option value="cashback" {{ old('type', $voucher->type) === 'cashback' ? 'selected' : '' }}>Cashback Coin</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori Voucher <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">Pilih Kategori</option>
                            <option value="all" {{ old('category', $voucher->category ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                            <option value="hijab" {{ old('category', $voucher->category) === 'hijab' ? 'selected' : '' }}>Hijab</option>
                            <option value="accessories" {{ old('category', $voucher->category) === 'accessories' ? 'selected' : '' }}>Accessories</option>
                            <option value="shoes" {{ old('category', $voucher->category) === 'shoes' ? 'selected' : '' }}>Shoes</option>
                            <option value="new_arrivals" {{ old('category', $voucher->category) === 'new_arrivals' ? 'selected' : '' }}>New Arrivals</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3" id="discountValueField" style="{{ $voucher->type === 'cashback' ? 'display: none;' : '' }}">
                        <label for="discount_value" class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('discount_value') is-invalid @enderror" 
                               id="discount_value" name="discount_value" value="{{ old('discount_value', $voucher->discount_value) }}" step="0.01" min="0" required>
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="maxDiscountField" style="{{ $voucher->type === 'percent' ? '' : 'display: none;' }}">
                        <label for="maximum_discount" class="form-label">Maksimum Diskon (untuk persentase)</label>
                        <input type="number" class="form-control @error('maximum_discount') is-invalid @enderror" 
                               id="maximum_discount" name="maximum_discount" value="{{ old('maximum_discount', $voucher->maximum_discount) }}" step="0.01" min="0">
                        @error('maximum_discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="cashbackField" style="{{ $voucher->type === 'cashback' ? '' : 'display: none;' }}">
                        <label for="cashback_coin" class="form-label">Cashback Coin</label>
                        <input type="number" class="form-control @error('cashback_coin') is-invalid @enderror" 
                               id="cashback_coin" name="cashback_coin" value="{{ old('cashback_coin', $voucher->cashback_coin) }}" min="0">
                        @error('cashback_coin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary bg-opacity-10">
                            <i class="fas fa-cog me-1"></i>Pengaturan
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="quota" class="form-label">Kuota <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quota') is-invalid @enderror" 
                                       id="quota" name="quota" value="{{ old('quota', $voucher->quota) }}" min="1" required>
                                @error('quota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="minimum_purchase" class="form-label">Minimum Pembelian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('minimum_purchase') is-invalid @enderror" 
                                           id="minimum_purchase" name="minimum_purchase" value="{{ old('minimum_purchase', $voucher->minimum_purchase) }}" min="0" required>
                                </div>
                                @error('minimum_purchase')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', $voucher->start_date->format('Y-m-d\TH:i')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', $voucher->end_date->format('Y-m-d\TH:i')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $voucher->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_displayed" name="is_displayed" {{ $voucher->is_displayed ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_displayed">Tampilkan ke User</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateCode() {
    const code = 'VOU-' + Math.random().toString(36).substring(2, 10).toUpperCase();
    document.querySelector('input[name="code"]').value = code;
}

function toggleDiscountFields() {
    const type = document.getElementById('voucherType').value;
    const discountValueField = document.getElementById('discountValueField');
    const maxDiscountField = document.getElementById('maxDiscountField');
    const cashbackField = document.getElementById('cashbackField');

    if (type === 'fixed') {
        discountValueField.style.display = 'block';
        maxDiscountField.style.display = 'none';
        cashbackField.style.display = 'none';
    } else if (type === 'percent') {
        discountValueField.style.display = 'block';
        maxDiscountField.style.display = 'block';
        cashbackField.style.display = 'none';
    } else if (type === 'cashback') {
        discountValueField.style.display = 'none';
        maxDiscountField.style.display = 'none';
        cashbackField.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleDiscountFields();
});
</script>
@endpush
