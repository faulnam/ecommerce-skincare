@extends('layouts.admin')

@section('title', 'Edit Diskon Ongkir')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 700;">Edit Diskon Ongkir</h4>
        <p class="text-muted mb-0">Ubah data diskon ongkos kirim</p>
    </div>
    <a href="{{ route('admin.shipping-discounts.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.shipping-discounts.update', $shippingDiscount) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Diskon <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $shippingDiscount->name) }}" placeholder="Contoh: Promo Akhir Tahun">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Persentase Diskon <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="discount_percent" class="form-control @error('discount_percent') is-invalid @enderror" 
                                   value="{{ old('discount_percent', $shippingDiscount->discount_percent) }}" min="0" max="100" step="0.01" placeholder="10">
                            <span class="input-group-text">%</span>
                        </div>
                        @error('discount_percent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="2" placeholder="Deskripsi singkat tentang promo ini">{{ old('description', $shippingDiscount->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Maksimal Diskon (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="max_discount" class="form-control @error('max_discount') is-invalid @enderror" 
                                   value="{{ old('max_discount', $shippingDiscount->max_discount) }}" min="0" placeholder="50000">
                        </div>
                        <small class="form-text text-muted">Batas maksimal diskon yang bisa didapat. Kosongkan jika tidak ada batas.</small>
                        @error('max_discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Minimal Subtotal (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="min_subtotal" class="form-control @error('min_subtotal') is-invalid @enderror" 
                                   value="{{ old('min_subtotal', $shippingDiscount->min_subtotal) }}" min="0" placeholder="100000">
                        </div>
                        <small class="form-text text-muted">Minimal belanja untuk mendapat diskon ini. Kosongkan jika tidak ada minimal.</small>
                        @error('min_subtotal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai (Opsional)</label>
                        <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                               value="{{ old('start_date', $shippingDiscount->start_date?->format('Y-m-d\TH:i')) }}">
                        <small class="form-text text-muted">Kosongkan jika diskon berlaku segera.</small>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Berakhir (Opsional)</label>
                        <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                               value="{{ old('end_date', $shippingDiscount->end_date?->format('Y-m-d\TH:i')) }}">
                        <small class="form-text text-muted">Kosongkan jika diskon berlaku selamanya.</small>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $shippingDiscount->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktifkan diskon ini</label>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.shipping-discounts.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
