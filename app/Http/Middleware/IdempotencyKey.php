<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyKey
{
    private const CACHE_TTL_HOURS = 24;
    private const LOCK_TTL_MINUTES = 5;
    private const PREFIX_LOCK = 'idempotency:lock:';
    private const PREFIX_RESULT = 'idempotency:result:';

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->extractKey($request);

        if (empty($key)) {
            return $next($request);
        }

        $lockKey   = self::PREFIX_LOCK . $key;
        $resultKey = self::PREFIX_RESULT . $key;

        // 1) Already completed -> replay cached response
        if (Cache::has($resultKey)) {
            return $this->replayResponse(Cache::get($resultKey));
        }

        // 2) Currently processing by another request -> 409 Conflict
        if (!Cache::add($lockKey, true, now()->addMinutes(self::LOCK_TTL_MINUTES))) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan sedang diproses. Silakan tunggu.',
            ], 409);
        }

        try {
            $response = $next($request);

            // 3) Only cache successful (2xx) responses
            if ($response->isSuccessful()) {
                $this->cacheResponse($resultKey, $response);
            }

            return $response;
        } finally {
            Cache::forget($lockKey);
        }
    }

    /**
     * Read idempotency key from header or form input.
     */
    private function extractKey(Request $request): ?string
    {
        $header = $request->header('Idempotency-Key');
        if (!empty($header) && is_string($header)) {
            return $header;
        }

        $input = $request->input('idempotency_key');
        if (!empty($input) && is_string($input)) {
            return $input;
        }

        return null;
    }

    /**
     * Serialize a response into a cache-friendly array.
     */
    private function cacheResponse(string $cacheKey, Response $response): void
    {
        $payload = [
            'status'  => $response->getStatusCode(),
            'headers' => $response->headers->all(),
        ];

        // JSON responses
        if ($this->isJsonResponse($response)) {
            $payload['type'] = 'json';
            $payload['body'] = $response->getContent();
        }
        // Redirect responses (store target URL + status code)
        elseif ($response->isRedirect()) {
            $payload['type'] = 'redirect';
            $payload['target_url'] = $response->headers->get('Location');
            $payload['status_code'] = $response->getStatusCode();
        }
        // Other responses (store raw content as base64)
        else {
            $payload['type'] = 'raw';
            $payload['body'] = base64_encode($response->getContent());
        }

        Cache::put($cacheKey, $payload, now()->addHours(self::CACHE_TTL_HOURS));
    }

    /**
     * Rebuild a response from cached payload.
     */
    private function replayResponse(array $payload): Response
    {
        $type = $payload['type'] ?? 'raw';

        if ($type === 'json') {
            return response($payload['body'], $payload['status'], $payload['headers'] ?? [])
                ->header('X-Idempotency-Replay', 'true');
        }

        if ($type === 'redirect') {
            return redirect($payload['target_url'], $payload['status_code'])
                ->header('X-Idempotency-Replay', 'true');
        }

        $content = base64_decode($payload['body']);
        return response($content, $payload['status'], $payload['headers'] ?? [])
            ->header('X-Idempotency-Replay', 'true');
    }

    private function isJsonResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'application/json');
    }
}
