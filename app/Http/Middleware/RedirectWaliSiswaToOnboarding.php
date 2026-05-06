<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectWaliSiswaToOnboarding
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->needsHubungkanAkunSekolahOnboarding()) {
            return $next($request);
        }

        if ($request->routeIs(
            'onboarding.*',
            'logout',
            'profile.*',
            'verification.*',
            'password.confirm',
            'password.update',
            'notifications.*',
            'ref.wilayah.*',
        )) {
            return $next($request);
        }

        return redirect()->route('onboarding.hubungkan');
    }
}
