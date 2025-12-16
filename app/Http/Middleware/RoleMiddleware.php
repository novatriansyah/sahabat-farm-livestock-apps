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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->role; // OWNER, STAFF, BREEDER

        // OWNER has global access (Super Admin)
        if ($userRole === 'OWNER') {
            return $next($request);
        }

        // Check if user's role is in the allowed list
        // $roles will be an array of strings passed from route definition
        // e.g. role:OWNER,BREEDER -> $roles = ['OWNER', 'BREEDER']
        // Note: Laravel middleware parameters are passed as variadic if separated by commas in 8.x+,
        // but traditionally handled as comma-separated string if simple.
        // Let's assume standard Laravel behaviour where multiple args come as variadic.

        // If the route definition was role:OWNER,BREEDER
        // Laravel passes handle($request, $next, 'OWNER', 'BREEDER')

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
