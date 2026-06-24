<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access the admin panel.');
        }

        // Check if user has admin role (role_id = 1)
        if (Auth::user()->role_id != 1) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'You do not have permission to access the admin panel.');
        }

        // Check admin session flag
        if (!session('is_admin')) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Invalid admin session. Please login again.');
        }

        return $next($request);
    }
}