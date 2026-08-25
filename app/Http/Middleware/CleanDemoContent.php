<?php

namespace App\Http\Middleware;

use App\Services\DemoCleanupService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanDemoContent
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Auto clean demo-created content older than 3 minutes
        DemoCleanupService::cleanExpired(3);

        return $next($request);
    }
}
