<?php

namespace App\Http\Middleware;

use App\Providers\ListsHelperServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserLists
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
        if (Auth::check()) {
            ListsHelperServiceProvider::createUserDefaultLists(Auth::user()->id);
        }

        return $next($request);
    }
} 