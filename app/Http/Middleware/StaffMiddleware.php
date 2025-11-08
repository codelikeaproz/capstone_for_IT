<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    /**
     * Handle an incoming request.
     * Allows access to users with staff or admin role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Allow both admin and staff to access
        if (!$user->isAdmin() && !$user->isStaff()) {
            abort(403, 'Only staff members and administrators can access this page.');
        }

        return $next($request);
    }
}
