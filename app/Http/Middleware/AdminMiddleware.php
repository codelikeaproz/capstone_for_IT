<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Allows access to users with admin or superadmin role.
     * (Both admin and superadmin can access admin pages)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Allow both superadmin and admin roles
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Only administrators can access this page.');
        }

        return $next($request);
    }
}
