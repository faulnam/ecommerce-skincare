<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->is_active) {
            return back()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Merge guest cart to user cart
            app(\App\Http\Controllers\Customer\CartController::class)->mergeGuestCart();
            
            // Merge guest wishlist to user wishlist
            app(\App\Http\Controllers\Customer\WishlistController::class)->mergeGuestWishlist();

            if (auth()->user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if (auth()->user()->isDeveloper()) {
                return redirect()->intended(route('admin.notification-logs.index'));
            }

            if (auth()->user()->isCourier()) {
                return redirect()->intended(route('courier.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->with('error', 'Email atau password salah.');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        return $this->requestRegisterOtp($request);
    }

    /**
     * Request OTP for registration
     */
    public function requestRegisterOtp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->whereNull('deleted_at')],
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'password' => ['required', 'confirmed', Password::min(8)],
            'cf-turnstile-response' => ['required', new \App\Rules\Turnstile],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'cf-turnstile-response.required' => 'Verifikasi Captcha wajib diisi.',
        ]);

        $defaultMailer = config('mail.default');
        if (in_array($defaultMailer, ['log', 'array'], true)) {
            return response()->json([
                'message' => 'Layanan email belum aktif. Silakan konfigurasi Gmail SMTP terlebih dahulu.',
            ], 422);
        }

        $otpCode = (string) random_int(100000, 999999);
        $cacheKey = $this->registrationOtpCacheKey($validated['email']);
        $ttlMinutes = 10;

        Cache::put($cacheKey, [
            'otp_hash' => Hash::make($otpCode),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'attempts' => 0,
        ], now()->addMinutes($ttlMinutes));

        try {
            Mail::raw(
                "Kode OTP registrasi Hijab Anda adalah: {$otpCode}\n\nKode berlaku {$ttlMinutes} menit. Jangan bagikan kode ini ke siapa pun.",
                function ($message) use ($validated) {
                    $message->to($validated['email'])
                        ->subject('Kode OTP Registrasi Hijab');
                }
            );
        } catch (\Throwable $exception) {
            Cache::forget($cacheKey);

            Log::error('Gagal mengirim OTP registrasi via SMTP.', [
                'email' => $validated['email'],
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'scheme' => config('mail.mailers.smtp.scheme'),
                'error' => $exception->getMessage(),
            ]);

            $message = 'Gagal mengirim OTP ke email. Periksa konfigurasi SMTP Gmail Anda.';

            $errorText = strtolower($exception->getMessage());

            if (str_contains($errorText, '535') || str_contains($errorText, 'username and password not accepted')) {
                $message = 'Autentikasi Gmail gagal. Gunakan App Password 16 digit (bukan password login Gmail biasa).';
            } elseif (str_contains($errorText, 'connection could not be established') || str_contains($errorText, 'timed out')) {
                $message = 'Koneksi ke server Gmail gagal. Periksa koneksi internet atau firewall.';
            } elseif (str_contains($errorText, 'expected response code') && str_contains($errorText, '530')) {
                $message = 'SMTP menolak request. Pastikan akun Gmail mengizinkan App Password dan 2-Step Verification aktif.';
            }

            return response()->json([
                'message' => $message,
            ], 500);
        }

        return response()->json([
            'message' => 'Kode OTP sudah dikirim ke email Anda.',
            'email' => $validated['email'],
            'ttl_minutes' => $ttlMinutes,
        ]);
    }

    /**
     * Verify OTP and complete registration
     */
    public function verifyRegisterOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit.',
        ]);

        $cacheKey = $this->registrationOtpCacheKey($validated['email']);
        $otpData = Cache::get($cacheKey);

        if (!$otpData) {
            return response()->json([
                'message' => 'Kode OTP tidak ditemukan atau sudah kadaluarsa. Silakan daftar ulang.',
            ], 422);
        }

        if (($otpData['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Terlalu banyak percobaan OTP. Silakan daftar ulang.',
            ], 429);
        }

        if (!Hash::check($validated['otp'], $otpData['otp_hash'])) {
            $otpData['attempts'] = ($otpData['attempts'] ?? 0) + 1;
            Cache::put($cacheKey, $otpData, now()->addMinutes(10));

            return response()->json([
                'message' => 'Kode OTP tidak valid.',
            ], 422);
        }

        if (User::where('email', $validated['email'])->exists()) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Email sudah terdaftar. Silakan login.',
            ], 422);
        }

        $deletedUser = User::onlyTrashed()->where('email', $validated['email'])->first();

        if ($deletedUser) {
            $deletedUser->restore();
            $deletedUser->update([
                'name' => $otpData['name'],
                'phone' => $otpData['phone'],
                'address' => $otpData['address'],
                'password' => $otpData['password'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $user = $deletedUser;
        } else {
            $user = User::create([
                'name' => $otpData['name'],
                'email' => $otpData['email'],
                'phone' => $otpData['phone'],
                'address' => $otpData['address'],
                'password' => $otpData['password'],
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
                'points' => 100,
                'welcome_bonus_claimed' => false,
                'first_purchase_completed' => false,
            ]);

            // Create welcome bonus point transaction with expiry
            $user->pointTransactions()->create([
                'points' => 100,
                'type' => 'welcome_bonus',
                'description' => 'Welcome bonus',
                'balance_before' => 0,
                'balance_after' => 100,
                'expires_at' => now()->addMonths(6),
            ]);
        }

        Cache::forget($cacheKey);

        Auth::login($user);

        // Merge guest cart to user cart
        app(\App\Http\Controllers\Customer\CartController::class)->mergeGuestCart();
        
        // Merge guest wishlist to user wishlist
        app(\App\Http\Controllers\Customer\WishlistController::class)->mergeGuestWishlist();

        return response()->json([
            'message' => 'Registrasi berhasil! Akun Anda sudah aktif.',
            'redirect' => route('home'),
        ]);
    }

    private function registrationOtpCacheKey(string $email): string
    {
        return 'register_otp:' . sha1(strtolower($email));
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        $user = User::withTrashed()->where('email', $googleUser->getEmail())->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
                $user->update(['is_active' => true]);
            }

            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            if (!$user->is_active) {
                return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.');
            }

            Auth::login($user, true);

            app(\App\Http\Controllers\Customer\CartController::class)->mergeGuestCart();
            app(\App\Http\Controllers\Customer\WishlistController::class)->mergeGuestWishlist();

            return redirect()->intended(route('home'));
        }

        $newUser = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password' => Hash::make(uniqid()),
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'points' => 100,
            'welcome_bonus_claimed' => false,
            'first_purchase_completed' => false,
        ]);

        // Create welcome bonus point transaction with expiry
        $newUser->pointTransactions()->create([
            'points' => 100,
            'type' => 'welcome_bonus',
            'description' => 'Welcome bonus',
            'balance_before' => 0,
            'balance_after' => 100,
            'expires_at' => now()->addMonths(6),
        ]);

        Auth::login($newUser, true);

        app(\App\Http\Controllers\Customer\CartController::class)->mergeGuestCart();
        app(\App\Http\Controllers\Customer\WishlistController::class)->mergeGuestWishlist();

        return redirect()->intended(route('home'));
    }

    /**
     * Handle Firebase authentication
     */
    public function handleFirebaseAuth(Request $request)
    {
        $validated = $request->validate([
            'idToken' => 'required|string',
            'displayName' => 'nullable|string',
            'email' => 'required|email',
            'photoURL' => 'nullable|string',
        ]);

        $user = User::withTrashed()->where('email', $validated['email'])->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
                $user->update(['is_active' => true]);
            }

            if (!$user->is_active) {
                return response()->json([
                    'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.',
                ], 403);
            }

            Auth::login($user, true);

            app(\App\Http\Controllers\Customer\CartController::class)->mergeGuestCart();
            app(\App\Http\Controllers\Customer\WishlistController::class)->mergeGuestWishlist();

            return response()->json([
                'message' => 'Login berhasil!',
                'redirect' => route('home'),
            ]);
        }

        $newUser = User::create([
            'name' => $validated['displayName'] ?? explode('@', $validated['email'])[0],
            'email' => $validated['email'],
            'password' => Hash::make(uniqid()),
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'points' => 100,
            'welcome_bonus_claimed' => false,
            'first_purchase_completed' => false,
        ]);

        // Create welcome bonus point transaction with expiry
        $newUser->pointTransactions()->create([
            'points' => 100,
            'type' => 'welcome_bonus',
            'description' => 'Welcome bonus',
            'balance_before' => 0,
            'balance_after' => 100,
            'expires_at' => now()->addMonths(6),
        ]);

        Auth::login($newUser, true);

        app(\App\Http\Controllers\Customer\CartController::class)->mergeGuestCart();
        app(\App\Http\Controllers\Customer\WishlistController::class)->mergeGuestWishlist();

        return response()->json([
            'message' => 'Registrasi berhasil!',
            'redirect' => route('home'),
        ]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Request OTP for password reset
     */
    public function requestPasswordResetOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.',
            ], 422);
        }

        $defaultMailer = config('mail.default');
        if (in_array($defaultMailer, ['log', 'array'], true)) {
            return response()->json([
                'message' => 'Layanan email belum aktif. Silakan konfigurasi Gmail SMTP terlebih dahulu.',
            ], 422);
        }

        $otpCode = (string) random_int(100000, 999999);
        $cacheKey = $this->passwordResetOtpCacheKey($validated['email']);
        $ttlMinutes = 10;

        Cache::put($cacheKey, [
            'otp_hash' => Hash::make($otpCode),
            'email' => $validated['email'],
            'attempts' => 0,
        ], now()->addMinutes($ttlMinutes));

        try {
            Mail::raw(
                "Kode OTP reset password Hijab Anda adalah: {$otpCode}\n\nKode berlaku {$ttlMinutes} menit. Jangan bagikan kode ini ke siapa pun.\n\nJika Anda tidak meminta reset password, abaikan email ini.",
                function ($message) use ($validated) {
                    $message->to($validated['email'])
                        ->subject('Kode OTP Reset Password Hijab');
                }
            );
        } catch (\Throwable $exception) {
            Cache::forget($cacheKey);

            Log::error('Gagal mengirim OTP reset password via SMTP.', [
                'email' => $validated['email'],
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal mengirim OTP ke email. Silakan coba lagi.',
            ], 500);
        }

        return response()->json([
            'message' => 'Kode OTP sudah dikirim ke email Anda.',
            'email' => $validated['email'],
            'ttl_minutes' => $ttlMinutes,
        ]);
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyPasswordResetOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit.',
        ]);

        $cacheKey = $this->passwordResetOtpCacheKey($validated['email']);
        $otpData = Cache::get($cacheKey);

        if (!$otpData) {
            return response()->json([
                'message' => 'Kode OTP tidak ditemukan atau sudah kadaluarsa. Silakan request ulang.',
            ], 422);
        }

        if (($otpData['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Terlalu banyak percobaan OTP. Silakan request ulang.',
            ], 429);
        }

        if (!Hash::check($validated['otp'], $otpData['otp_hash'])) {
            $otpData['attempts'] = ($otpData['attempts'] ?? 0) + 1;
            Cache::put($cacheKey, $otpData, now()->addMinutes(10));

            return response()->json([
                'message' => 'Kode OTP tidak valid.',
            ], 422);
        }

        // OTP valid, mark as verified
        $otpData['verified'] = true;
        Cache::put($cacheKey, $otpData, now()->addMinutes(10));

        return response()->json([
            'message' => 'Kode OTP valid. Silakan masukkan password baru.',
            'verified' => true,
        ]);
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $cacheKey = $this->passwordResetOtpCacheKey($validated['email']);
        $otpData = Cache::get($cacheKey);

        if (!$otpData || !($otpData['verified'] ?? false)) {
            return response()->json([
                'message' => 'Sesi reset password tidak valid. Silakan verifikasi OTP terlebih dahulu.',
            ], 422);
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            Cache::forget($cacheKey);
            return response()->json([
                'message' => 'User tidak ditemukan.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        Cache::forget($cacheKey);

        return response()->json([
            'message' => 'Password berhasil direset. Silakan login dengan password baru.',
            'redirect' => route('login'),
        ]);
    }

    private function passwordResetOtpCacheKey(string $email): string
    {
        return 'password_reset_otp:' . sha1(strtolower($email));
    }
}
