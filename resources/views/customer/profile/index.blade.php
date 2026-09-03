@extends('layouts.app')

@section('title', 'My Profile - LUMINA')

@section('content')
@php
    $jsonPath = public_path('translation/customer.json');
    $custTrans = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
@endphp
<div class="bg-white text-black antialiased">
    @include('components.luxury-navbar')

<div class="mx-auto w-full max-w-7xl px-6 pb-8 pt-32 md:px-10 md:pb-12 md:pt-32 lg:px-12 lg:pb-16 lg:pt-40">
    <h3 class="mb-6 text-3xl font-semibold tracking-tight text-black sm:text-4xl">
        <i class="fas fa-user mr-3 text-black"></i>{{ $custTrans['page_title'][$lang] ?? 'My Profile' }}
    </h3>
    
    
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <!-- Profile Card -->
            <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                <div class="px-6 py-8 text-center">
                    <!-- Avatar with upload -->
                    <div class="relative mb-4 inline-block">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                             class="h-28 w-28 rounded-full border-4 border-black object-cover" id="avatarPreview">
                        <label for="avatarInput" class="absolute bottom-0 right-0 flex h-9 w-9 cursor-pointer items-center justify-center rounded-full border-3 border-white bg-black text-white transition hover:bg-black/90"> 
                            <i class="fas fa-camera text-xs"></i>
                        </label>
                    </div>
                    
                    <form action="{{ route('customer.profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" class="hidden">
                    </form>
                    
                    @error('avatar')
                        <div class="mb-2 text-sm text-rose-600">{{ $message }}</div>
                    @enderror
                    
                    <h5 class="mb-1 text-xl font-semibold text-black">{{ $user->name }}</h5>
                    <p class="mb-3 text-sm text-zinc-500">{{ $user->email }}</p>
                    <span class="inline-block rounded-full bg-black px-3 py-1 text-xs font-medium text-white">{{ $custTrans['badge_customer'][$lang] ?? 'Customer' }}</span>
                    
                    <!-- Points Display -->
                    <div class="mt-4 rounded-xl bg-gradient-to-r from-zinc-900 to-black px-4 py-3 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-white/80">{{ $custTrans['points_title'][$lang] ?? 'Loyalty Points' }}</p>
                                <p class="text-2xl font-bold">{{ number_format($user->available_points) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-white/80">{{ $custTrans['points_value'][$lang] ?? 'Value' }}</p>
                                <p class="text-sm font-semibold">{{ $user->formatted_points_value }}</p>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-white/70">{{ $custTrans['points_note'][$lang] ?? '100 Points = Rp10,000 (1% cashback)' }}</p>
                        @if($user->next_points_expiry)
                            <p class="mt-1 text-[11px] text-amber-200"><i class="far fa-clock mr-1"></i>{{ $custTrans['points_expiry'][$lang] ?? 'Berlaku sampai:' }} {{ $user->next_points_expiry->format('d M Y') }}</p>
                        @endif
                    </div>
                </div>
                <div class="border-t border-black/6">
                    <a href="{{ route('customer.vouchers.my-vouchers') }}" class="flex items-center justify-between border-b border-black/6 px-6 py-3 transition hover:bg-zinc-50">
                        <span class="text-sm text-zinc-600"><i class="fas fa-ticket-alt mr-2"></i>{{ $custTrans['menu_vouchers'][$lang] ?? 'My Vouchers' }}</span>
                        <span class="text-sm font-medium text-zinc-900">
                            {{ auth()->user()->vouchers()->count() }} <i class="fas fa-chevron-right text-xs ml-1"></i>
                        </span>
                    </a>
                    <a href="{{ route('customer.profile.rewards') }}" class="flex items-center justify-between border-b border-black/6 px-6 py-3 transition hover:bg-zinc-50">
                        <span class="text-sm text-zinc-600"><i class="fas fa-gift mr-2"></i>{{ $custTrans['menu_rewards'][$lang] ?? 'Reward & Points' }}</span>
                        <span class="text-sm font-medium text-zinc-900">{{ $custTrans['menu_rewards_view'][$lang] ?? 'Lihat' }} <i class="fas fa-chevron-right text-xs ml-1"></i></span>
                    </a>
                    <div class="flex items-center justify-between border-b border-black/6 px-6 py-3">
                        <span class="text-sm text-zinc-600"><i class="fas fa-phone mr-2"></i>{{ $custTrans['label_phone'][$lang] ?? 'Phone' }}</span>
                        <span class="text-sm font-medium text-black">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-6 py-3">
                        <span class="text-sm text-zinc-600"><i class="fas fa-calendar mr-2"></i>{{ $custTrans['label_joined'][$lang] ?? 'Joined' }}</span>
                        <span class="text-sm font-medium text-black">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                <!-- Tombol Logout -->
                <div class="border-t border-black/6 px-6 py-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-full border border-rose-600 bg-white px-4 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>{{ $custTrans['btn_logout'][$lang] ?? 'Logout' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="space-y-6 lg:col-span-2">
            <!-- Update Profile -->
            <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                <div class="border-b border-black/6 bg-zinc-50 px-6 py-4">
                    <h4 class="text-lg font-semibold text-black"><i class="fas fa-edit mr-2"></i>{{ $custTrans['section_edit_profile'][$lang] ?? 'Edit Profile' }}</h4>
                </div>
                <div class="px-6 py-6">
                    <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <label class="mb-2 block text-sm font-medium text-black">{{ $custTrans['input_name'][$lang] ?? 'Full Name' }}</label>
                            <input type="text" class="w-full rounded-xl border border-black/10 px-4 py-2.5 text-sm transition focus:border-black focus:outline-none focus:ring-1 focus:ring-black @error('name') border-rose-500 @enderror" 
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="mb-2 block text-sm font-medium text-black">{{ $custTrans['input_email'][$lang] ?? 'Email' }}</label>
                            <input type="email" class="w-full rounded-xl border border-black/10 bg-zinc-50 px-4 py-2.5 text-sm text-zinc-500" value="{{ $user->email }}" disabled>
                            <p class="mt-1 text-xs text-zinc-500">{{ $custTrans['email_note'][$lang] ?? 'Email cannot be changed' }}</p>
                        </div>
                        
                        <div>
                            <label class="mb-2 block text-sm font-medium text-black">{{ $custTrans['input_phone'][$lang] ?? 'Phone Number' }}</label>
                            <input type="text" class="w-full rounded-xl border border-black/10 px-4 py-2.5 text-sm transition focus:border-black focus:outline-none focus:ring-1 focus:ring-black @error('phone') border-rose-500 @enderror" 
                                   name="phone" value="{{ old('phone', $user->phone) }}" required>
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="mb-2 block text-sm font-medium text-black">{{ $custTrans['input_address'][$lang] ?? 'Address' }}</label>
                            <textarea class="w-full rounded-xl border border-black/10 px-4 py-2.5 text-sm transition focus:border-black focus:outline-none focus:ring-1 focus:ring-black @error('address') border-rose-500 @enderror" 
                                      name="address" rows="3" required>{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" class="rounded-full bg-black px-6 py-2.5 text-sm font-medium text-white transition hover:bg-black/90">
                            <i class="fas fa-save mr-2"></i>{{ $custTrans['btn_save'][$lang] ?? 'Save Changes' }}
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Change Password -->
            <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                <div class="border-b border-black/6 bg-zinc-50 px-6 py-4">
                    <h4 class="text-lg font-semibold text-black"><i class="fas fa-lock mr-2"></i>{{ $custTrans['section_password'][$lang] ?? 'Change Password' }}</h4>
                </div>
                <div class="px-6 py-6">
                    <form action="{{ route('customer.profile.update-password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="mb-2 block text-sm font-medium text-black">{{ $custTrans['input_current_password'][$lang] ?? 'Current Password' }}</label>
                            <input type="password" class="w-full rounded-xl border border-black/10 px-4 py-2.5 text-sm transition focus:border-black focus:outline-none focus:ring-1 focus:ring-black @error('current_password') border-rose-500 @enderror"
                                   name="current_password" required>
                            @error('current_password')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-black">{{ $custTrans['input_new_password'][$lang] ?? 'New Password' }}</label>
                            <input type="password" class="w-full rounded-xl border border-black/10 px-4 py-2.5 text-sm transition focus:border-black focus:outline-none focus:ring-1 focus:ring-black @error('password') border-rose-500 @enderror"
                                   name="password" required>
                            @error('password')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-zinc-500">{{ $custTrans['password_note'][$lang] ?? 'Minimum 8 characters' }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-black">{{ $custTrans['input_confirm_password'][$lang] ?? 'Confirm New Password' }}</label>
                            <input type="password" class="w-full rounded-xl border border-black/10 px-4 py-2.5 text-sm transition focus:border-black focus:outline-none focus:ring-1 focus:ring-black" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="rounded-full border border-black bg-white px-6 py-2.5 text-sm font-medium text-black transition hover:bg-black hover:text-white">
                            <i class="fas fa-key mr-2"></i>{{ $custTrans['btn_change_password'][$lang] ?? 'Change Password' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danger Zone: Delete Account -->
            <div class="overflow-hidden rounded-2xl border border-rose-200 bg-rose-50/30 shadow-sm" x-data="deleteAccountFlow()">
                <div class="border-b border-rose-200 bg-rose-50 px-6 py-4">
                    <h4 class="text-lg font-semibold text-rose-600"><i class="fas fa-exclamation-triangle mr-2"></i>{{ $custTrans['section_delete_account'][$lang] ?? 'Delete Account' }}</h4>
                </div>
                <div class="px-6 py-6">
                    <p class="mb-4 text-sm text-rose-700/80">
                        {{ $custTrans['delete_account_warning'][$lang] ?? 'Peringatan: Tindakan ini akan menonaktifkan akun Anda. Namun, riwayat pesanan dan data terkait email Anda akan tetap tersimpan. Jika Anda mendaftar kembali dengan email ini di masa depan, riwayat Anda akan dikembalikan.' }}
                    </p>

                    <!-- Messages -->
                    <template x-if="errorMsg">
                        <div class="mb-4 rounded-xl bg-rose-100 p-3 text-sm text-rose-700" x-text="errorMsg"></div>
                    </template>
                    <template x-if="successMsg">
                        <div class="mb-4 rounded-xl bg-emerald-100 p-3 text-sm text-emerald-700" x-text="successMsg"></div>
                    </template>
                    
                    @error('otp')
                        <div class="mb-4 rounded-xl bg-rose-100 p-3 text-sm text-rose-700">{{ $message }}</div>
                    @enderror
                    @error('email')
                        <div class="mb-4 rounded-xl bg-rose-100 p-3 text-sm text-rose-700">{{ $message }}</div>
                    @enderror

                    <!-- Step 1: Input Email -->
                    <div x-show="step === 1" class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-rose-900">Email Akun Anda</label>
                            <input type="email" x-model="email" class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm transition focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500"
                                   placeholder="Masukkan email terdaftar" required>
                        </div>
                        <button type="button" @click="requestOtp" :disabled="isLoading || !email" class="rounded-full bg-rose-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-rose-700 disabled:opacity-50">
                            <i class="fas fa-paper-plane mr-2" x-show="!isLoading"></i>
                            <i class="fas fa-spinner fa-spin mr-2" x-show="isLoading" style="display: none;"></i>
                            <span x-text="isLoading ? 'Mengirim...' : 'Kirim OTP Hapus Akun'"></span>
                        </button>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <form x-show="step === 2" action="{{ route('customer.profile.destroy') }}" method="POST" class="space-y-4" onsubmit="return confirm('Apakah Anda sangat yakin ingin menghapus akun Anda secara permanen?');" style="display: none;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="email" :value="email">
                        
                        <div>
                            <label class="mb-2 block text-sm font-medium text-rose-900">Kode OTP (Cek Email Anda)</label>
                            <input type="text" name="otp" class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm transition focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500"
                                   placeholder="6 Digit OTP" required maxlength="6" pattern="[0-9]{6}">
                        </div>

                        <button type="submit" class="rounded-full bg-rose-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-rose-700">
                            <i class="fas fa-trash-alt mr-2"></i>Konfirmasi & Hapus Akun
                        </button>
                        <button type="button" @click="step = 1; errorMsg = ''; successMsg = '';" class="ml-2 rounded-full border border-rose-200 bg-white px-6 py-2.5 text-sm font-medium text-rose-700 transition hover:bg-rose-50">
                            Batal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order History -->
            <div class="overflow-hidden rounded-2xl border border-black/6 bg-white shadow-sm">
                <div class="border-b border-black/6 bg-zinc-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-black"><i class="fas fa-shopping-bag mr-2"></i>{{ $custTrans['section_orders'][$lang] ?? 'Recent Orders' }}</h4>
                        <a href="{{ route('customer.orders.index') }}" class="text-sm font-medium text-zinc-900 hover:text-black">{{ $custTrans['link_view_all'][$lang] ?? 'View All' }} <i class="fas fa-chevron-right text-xs ml-1"></i></a>
                    </div>
                </div>
                <div class="px-6 py-6">
                    @forelse($orders as $order)
                        <div class="mb-4 last:mb-0 rounded-xl border border-black/6 p-4 hover:border-black/10 transition">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <h5 class="font-semibold text-black">{{ $order->order_number }}</h5>
                                    <p class="text-xs text-zinc-500">{{ $order->created_at->format('d F Y, H:i') }}</p>
                                </div>
                                @php
                                    $statusColors = [
                                        'pending_payment' => 'bg-amber-100 text-amber-800',
                                        'paid' => 'bg-blue-100 text-blue-800',
                                        'processing' => 'bg-purple-100 text-purple-800',
                                        'shipped' => 'bg-indigo-100 text-indigo-800',
                                        'delivered' => 'bg-emerald-100 text-emerald-800',
                                        'completed' => 'bg-emerald-100 text-emerald-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusColor = $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-800';
                                @endphp
                               <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">{{ $order->status_label }}</span>
                            </div>
                            <div class="mb-3 space-y-1.5">
                                @foreach($order->items->take(2) as $item)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-zinc-700">{{ $item->product_name }} <span class="text-zinc-400">×{{ $item->quantity }}</span></span>
                                        <span class="font-medium text-black">{{ $item->formatted_subtotal }}</span>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <p class="text-xs text-zinc-500">{{ str_replace(':count', $order->items->count() - 2, $custTrans['label_other_items'][$lang] ?? '+' . ($order->items->count() - 2) . ' item lainnya') }}</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between border-t border-zinc-100 pt-3">
                                <div>
                                    <p class="text-xs text-zinc-500">{{ $custTrans['label_total'][$lang] ?? 'Total' }}</p>
                                    <p class="text-sm font-semibold text-black">{{ $order->formatted_total }}</p>
                                </div>
                                <a href="{{ route('customer.orders.show', $order) }}"
                                   class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-medium text-black hover:bg-zinc-50 transition">
                                    {{ $custTrans['btn_view_details'][$lang] ?? 'View Details' }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <div class="mb-3 flex h-14 w-14 mx-auto items-center justify-center rounded-full bg-zinc-100">
                                <i class="fas fa-shopping-bag text-2xl text-zinc-400"></i>
                            </div>
                            <h5 class="mb-1 text-sm font-semibold text-black">{{ $custTrans['empty_orders_title'][$lang] ?? 'No Orders Yet' }}</h5>
                            <p class="mb-4 text-xs text-zinc-600">{{ $custTrans['empty_orders_desc'][$lang] ?? "Let's start shopping for skincare equipment!" }}</p>
                            <a href="{{ route('new-arrivals') }}"
                               class="inline-block rounded-lg bg-black px-4 py-2 text-xs font-medium text-white hover:bg-black/90 transition">
                                {{ $custTrans['btn_start_shopping'][$lang] ?? 'Start Shopping' }}
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        
    </div>
</div>


</div>

@push('styles')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@push('scripts')
<script>
(function() {
    const avatarInput = document.getElementById('avatarInput');

    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
                document.getElementById('avatarForm').submit();
            }
        });
    }
})();

document.addEventListener('alpine:init', () => {
    Alpine.data('deleteAccountFlow', () => ({
        step: 1,
        email: '',
        isLoading: false,
        errorMsg: '',
        successMsg: '',

        requestOtp() {
            if (!this.email) {
                this.errorMsg = 'Email wajib diisi.';
                return;
            }

            this.isLoading = true;
            this.errorMsg = '';
            this.successMsg = '';

            fetch('{{ route("customer.profile.request-delete-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: this.email
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isLoading = false;
                if (data.success) {
                    this.step = 2;
                    this.successMsg = data.message;
                } else {
                    this.errorMsg = data.message || 'Terjadi kesalahan.';
                }
            })
            .catch(error => {
                this.isLoading = false;
                this.errorMsg = 'Koneksi bermasalah. Silakan coba lagi.';
                console.error('Error:', error);
            });
        }
    }));
});
</script>
@endpush
@endsection