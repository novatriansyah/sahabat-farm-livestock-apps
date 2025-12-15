<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->role; // OWNER, STAFF, BREEDER

        // Simple check: OWNER has access to everything.
        if ($userRole === 'OWNER') {
            return $next($request);
        }

        // STAFF/BREEDER access check
        if ($role === 'STAFF' && ($userRole === 'STAFF' || $userRole === 'BREEDER')) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
