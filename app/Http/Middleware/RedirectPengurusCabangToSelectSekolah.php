<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPengurusCabangToSelectSekolah
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('pengurus_cabang')) {
            return $next($request);
        }

        if ($request->routeIs('pengurus.*', 'logout', 'profile.*', 'notifications.*', 'ref.wilayah.*', 'dashboard')) {
            return $next($request);
        }

        if (! session('pengurus_sekolah_id')) {
            return redirect()->route('pengurus.sekolah.index');
        }

        return $next($request);
    }
}
