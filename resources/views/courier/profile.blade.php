@extends('layouts.courier')

@section('title', 'Profil Saya')

@section('content')
@php
    $jsonPath = public_path('translation/courierprofile.json');
    $cpTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('courier.dashboard') }}" class="text-decoration-none" style="color: var(--primary);">
                {{ $cpTrans['bc_dashboard'][$lang] ?? 'Dashboard' }}
            </a>
        </li>
        <li class="breadcrumb-item active">{{ $cpTrans['bc_profile'][$lang] ?? 'Profil' }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 700; color: var(--dark);">{{ $cpTrans['page_title'][$lang] ?? 'Profil Saya' }}</h4>
        <p class="text-muted mb-0" style="font-size: 13px;">{{ $cpTrans['page_subtitle'][$lang] ?? 'Kelola informasi akun Anda' }}</p>
    </div>
</div>

{{-- Flash Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; font-size: 13px; background-color: #dcfce7; color: #16a34a;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; font-size: 13px; background-color: #ffe4e6; color: #e11d48;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    {{-- Left Column: Avatar & Counter Statistics --}}
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-body text-center py-4">
                <!-- Avatar Framework container with upload trigger -->
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                         class="rounded-circle shadow-sm" id="avatarPreview"
                         style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #003C52;">
                    <label for="avatarInput" class="position-absolute bottom-0 end-0 text-white rounded-circle d-flex align-items-center justify-content-center transition shadow" 
                           style="width: 36px; height: 36px; cursor: pointer; border: 3px solid white; background-color: #003C52;">
                        <i class="fas fa-camera" style="font-size: 12px;"></i>
                    </label>
                </div>
                
                <form action="{{ route('courier.profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none">
                </form>
                
                @error('avatar')
                    <div class="text-danger small mb-2 fw-medium" style="font-size: 12px;">{{ $message }}</div>
                @enderror
                
                <h5 class="mb-1" style="font-weight: 700; color: var(--dark); font-size: 16px;">{{ auth()->user()->name }}</h5>
                <p class="text-muted small mb-4">{{ $cpTrans['role_courier'][$lang] ?? 'Kurir' }}</p>
                
                <div class="d-flex justify-content-center gap-4 text-center border-top pt-3 mx-2">
                    <div>
                        <h5 class="mb-0" style="font-weight: 700; color: #10b981; font-size: 18px;">{{ auth()->user()->completedDeliveries()->count() }}</h5>
                        <small class="text-muted" style="font-size: 11px; font-weight: 500;">{{ $cpTrans['stat_completed'][$lang] ?? 'Pengiriman Selesai' }}</small>
                    </div>
                    <div class="border-end style-border" style="height: 32px; width: 1px; background-color: #f1f5f9;"></div>
                    <div>
                        <h5 class="mb-0" style="font-weight: 700; color: #003C52; font-size: 18px;">{{ auth()->user()->assignedDeliveries()->count() }}</h5>
                        <small class="text-muted" style="font-size: 11px; font-weight: 500;">{{ $cpTrans['stat_assigned'][$lang] ?? 'Total Ditugaskan' }}</small>
                    </div>
                </div>

                <!-- Mobile Responsive Sign-Out Action Trigger -->
                <div class="mt-4 d-lg-none px-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 py-2 transition" style="border-radius: 10px; font-size: 13px; font-weight: 600;">
                            <i class="fas fa-sign-out-alt me-2"></i>{{ $cpTrans['btn_logout'][$lang] ?? 'Keluar' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Right Column: Information Mutator Forms --}}
    <div class="col-lg-8">
        <!-- Section: General Account Metadata Info -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 border-bottom border-light" style="font-weight: 600; font-size: 14px; color: var(--dark);">
                <i class="fas fa-user-edit me-2" style="color: #003C52;"></i>{{ $cpTrans['card_edit_title'][$lang] ?? 'Update Profil' }}
            </div>
            <div class="card-body p-4" style="font-size: 13px;">
                <form action="{{ route('courier.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label text-zinc-700 fw-medium">{{ $cpTrans['label_name'][$lang] ?? 'Nama Lengkap' }}</label>
                        <input type="text" name="name" class="form-control py-2 border-zinc-200 @error('name') is-invalid @enderror" 
                               value="{{ old('name', auth()->user()->name) }}" style="border-radius: 10px;" required>
                        @error('name')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-zinc-700 fw-medium">{{ $cpTrans['label_email'][$lang] ?? 'Email' }}</label>
                        <input type="email" name="email" class="form-control py-2 border-zinc-200 @error('email') is-invalid @enderror" 
                               value="{{ old('email', auth()->user()->email) }}" style="border-radius: 10px;" required>
                        @error('email')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-zinc-700 fw-medium">{{ $cpTrans['label_phone'][$lang] ?? 'No. Telepon' }}</label>
                        <input type="text" name="phone" class="form-control py-2 border-zinc-200 @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', auth()->user()->phone) }}" style="border-radius: 10px;">
                        @error('phone')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-zinc-700 fw-medium">{{ $cpTrans['label_address'][$lang] ?? 'Alamat' }}</label>
                        <textarea name="address" class="form-control border-zinc-200 @error('address') is-invalid @enderror" 
                                  rows="3" style="border-radius: 10px; padding: 10px;">{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn text-white px-4 py-2 shadow-sm transition-all" style="background-color: #003C52; border-radius: 999px; font-weight: 600;">
                        <i class="fas fa-save me-1.5"></i>{{ $cpTrans['btn_save'][$lang] ?? 'Simpan Perubahan' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Section: Security Password Mutator Info -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 border-bottom border-light" style="font-weight: 600; font-size: 14px; color: var(--dark);">
                <i class="fas fa-lock me-2" style="color: #f59e0b;"></i>{{ $cpTrans['card_pwd_title'][$lang] ?? 'Ubah Password' }}
            </div>
            <div class="card-body p-4" style="font-size: 13px;">
                <form action="{{ route('courier.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label text-zinc-700 fw-medium">{{ $cpTrans['label_curr_pwd'][$lang] ?? 'Password Saat Ini' }}</label>
                        <input type="password" name="current_password" class="form-control py-2 border-zinc-200 @error('current_password') is-invalid @enderror" style="border-radius: 10px;" required>
                        @error('current_password')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-zinc-700 fw-medium">{{ $cpTrans['label_new_pwd'][$lang] ?? 'Password Baru' }}</label>
                        <input type="password" name="password" class="form-control py-2 border-zinc-200 @error('password') is-invalid @enderror" style="border-radius: 10px;" required>
                        @error('password')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-zinc-700 fw-medium">{{ $cpTrans['label_conf_pwd'][$lang] ?? 'Konfirmasi Password Baru' }}</label>
                        <input type="password" name="password_confirmation" class="form-control py-2 border-zinc-200" style="border-radius: 10px;" required>
                    </div>
                    
                    <button type="submit" class="btn text-white px-4 py-2 shadow-sm transition-all" style="background-color: #f59e0b; border-radius: 999px; font-weight: 600;">
                        <i class="fas fa-key me-1.5"></i>{{ $cpTrans['btn_change_pwd'][$lang] ?? 'Ubah Password' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('avatarInput').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
        
        document.getElementById('avatarForm').submit();
    }
});
</script>
@endpush
@endsection