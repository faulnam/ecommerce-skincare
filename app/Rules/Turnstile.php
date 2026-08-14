<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->retry(2, 300)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $value
                    // remoteip dihapus karena sering error 400 jika berada di belakang Cloudflare Tunnel / Proxy
                ]);

            if ($response->json('success') === true) {
                return true;
            }

            // Log alasan gagal asli dari Cloudflare (mis. timeout-or-duplicate,
            // invalid-input-secret, invalid-input-response) supaya bisa didiagnosa.
            Log::warning('Turnstile verification failed', [
                'error_codes' => $response->json('error-codes'),
                'status' => $response->status(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Turnstile validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Verifikasi Captcha gagal, silakan coba lagi.';
    }
}
