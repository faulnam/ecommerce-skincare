<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visitor;
use Carbon\Carbon;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Skip tracking for static assets, ajax, or bot requests if needed.
            if (!$request->ajax() && !$request->wantsJson()) {
                $ip = $request->ip();
                $date = Carbon::today()->toDateString();

                $visitor = Visitor::firstOrCreate(
                    ['ip_address' => $ip, 'date' => $date],
                    ['user_agent' => $request->userAgent(), 'views' => 0]
                );

                $visitor->increment('views');
            }
        } catch (\Exception $e) {
            // Silently fail if tracking fails to not disrupt user experience
        }

        return $next($request);
    }
}
