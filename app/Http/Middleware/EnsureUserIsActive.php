<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Validate that authenticated users keep access only while active.
     *
     * Scenario covered:
     * 1) Admin deactivates a user in the panel.
     * 2) User still has an active session in another browser tab.
     * 3) This middleware logs out that user in the next request.
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->is_active) {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Tu cuenta está desactivada. Contacta a un administrador.']);
    }
}
