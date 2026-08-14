<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show profile
     */
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->take(5)->get();
        return view('customer.profile.index', compact('user', 'orders'));
    }

    /**
     * Show rewards & points page
     */
    public function rewards()
    {
        $user = auth()->user()->load('pointTransactions');
        $pointTransactions = $user->pointTransactions()->latest()->paginate(15);

        return view('customer.profile.rewards', compact('user', 'pointTransactions'));
    }

    /**
     * Show user's vouchers
     */
    public function vouchers()
    {
        $user = auth()->user();
        $vouchers = $user->vouchers()->withPivot('claimed_at', 'is_used', 'used_at')->latest('user_vouchers.claimed_at')->paginate(15);

        return view('customer.profile.vouchers', compact('user', 'vouchers'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Pilih foto terlebih dahulu.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Password saat ini salah.');
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Request OTP for account deletion
     */
    public function requestDeleteOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = auth()->user();

        if ($request->email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak sesuai dengan akun Anda.'
            ], 400);
        }

        $otpCode = (string) random_int(100000, 999999);
        $cacheKey = 'delete_account_otp:' . $user->id;
        $ttlMinutes = 10;

        $otpData = [
            'otp_hash' => Hash::make($otpCode),
            'attempts' => 0,
            'expires_at' => now()->addMinutes($ttlMinutes)->timestamp,
        ];

        Cache::put($cacheKey, $otpData, now()->addMinutes($ttlMinutes));

        try {
            Mail::raw(
                "Kode OTP penghapusan akun Hijab Anda adalah: {$otpCode}\n\nKode berlaku {$ttlMinutes} menit. Jika Anda tidak merasa melakukan permintaan ini, segera amankan akun Anda.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Kode OTP Penghapusan Akun Hijab');
                }
            );
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim OTP penghapusan akun via SMTP.', [
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP ke email. Silakan coba lagi nanti.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP penghapusan akun telah dikirim ke email Anda.'
        ]);
    }

    /**
     * Delete account
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit.',
        ]);

        $user = auth()->user();

        if ($validated['email'] !== $user->email) {
            return back()->with('error', 'Email tidak sesuai dengan akun Anda.');
        }

        $cacheKey = 'delete_account_otp:' . $user->id;
        $otpData = Cache::get($cacheKey);

        if (!$otpData) {
            return back()->with('error', 'Kode OTP tidak ditemukan atau sudah kadaluarsa. Silakan request ulang.');
        }

        if (($otpData['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);
            return back()->with('error', 'Terlalu banyak percobaan OTP. Silakan request ulang.');
        }

        if (!Hash::check($validated['otp'], $otpData['otp_hash'])) {
            $otpData['attempts'] = ($otpData['attempts'] ?? 0) + 1;
            Cache::put($cacheKey, $otpData, now()->addMinutes(10));
            return back()->with('error', 'Kode OTP tidak valid.');
        }

        // OTP valid, forget cache and delete user
        Cache::forget($cacheKey);

        // Soft delete the user
        $user->delete();

        // Logout
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Akun Anda berhasil dihapus.');
    }
}
