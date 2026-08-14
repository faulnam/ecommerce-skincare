<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin() && !auth()->user()->isDeveloper() && !auth()->user()->isBlogger()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
        }

        $isLogRoute = $request->routeIs('admin.notification-logs.*') || 
                      $request->routeIs('admin.biteship-logs.*') || 
                      $request->routeIs('admin.paylabs-logs.*');

        if (auth()->user()->isDeveloper()) {
            if (!$isLogRoute && !$request->routeIs('admin.profile.*') && !$request->routeIs('logout')) {
                return redirect()->route('admin.notification-logs.index');
            }
        } else if (auth()->user()->isAdmin()) {
            if ($isLogRoute) {
                abort(403, 'Akses ditolak. Halaman ini hanya untuk Developer.');
            }
        } else if (auth()->user()->isBlogger()) {
            if (!$request->routeIs('admin.dashboard') && 
                !$request->routeIs('admin.insights.*') && 
                !$request->routeIs('admin.insights.search-products') && 
                !$request->routeIs('admin.profile.*') && 
                !$request->routeIs('logout')) {
                abort(403, 'Akses ditolak. Blogger hanya dapat mengakses menu Insight.');
            }
        }

        if (!auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan.');
        }

        return $next($request);
    }
}
