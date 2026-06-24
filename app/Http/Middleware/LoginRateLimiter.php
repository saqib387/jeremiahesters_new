<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use App\Model\User;
use Carbon\Carbon;

class LoginRateLimiter
{
    /**
     * Maximum login attempts before lockout
     */
    protected $maxAttempts = 5;

    /**
     * Lockout duration in minutes
     */
    protected $decayMinutes = 15;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $this->throttleKey($request);
        
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Log suspicious activity
            Log::channel('security')->warning('Rate limit exceeded for login', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'user_agent' => $request->userAgent(),
                'lockout_seconds' => $seconds,
            ]);

            return $this->tooManyAttempts($request, $seconds);
        }

        $response = $next($request);

        // If login failed, increment rate limiter
        if ($response->getStatusCode() === 422 || 
            ($response->getStatusCode() === 302 && session()->has('errors'))) {
            RateLimiter::hit($key, $this->decayMinutes * 60);
            
            // Update user login attempts if they exist
            $this->incrementUserLoginAttempts($request->input('email'));
        } else {
            // Successful login - clear rate limiter
            RateLimiter::clear($key);
            
            // Clear user login attempts and update last login
            $this->clearUserLoginAttempts($request->input('email'), $request);
        }

        return $response;
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request)
    {
        return 'login:' . $request->ip() . '|' . strtolower($request->input('email', ''));
    }

    /**
     * Handle too many attempts response
     */
    protected function tooManyAttempts(Request $request, $seconds)
    {
        $minutes = ceil($seconds / 60);
        $message = __("Too many login attempts. Please try again in :minutes minutes.", ['minutes' => $minutes]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'error' => 'rate_limit_exceeded',
                'message' => $message,
                'retry_after' => $seconds,
            ], 429);
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $message]);
    }

    /**
     * Increment user's login attempts counter
     */
    protected function incrementUserLoginAttempts($email)
    {
        if (!$email) return;

        $user = User::where('email', strtolower($email))->first();
        if ($user) {
            $user->login_attempts = ($user->login_attempts ?? 0) + 1;
            
            // Lock account after 10 failed attempts
            if ($user->login_attempts >= 10) {
                $user->locked_until = Carbon::now()->addMinutes(30);
                
                Log::channel('security')->warning('Account locked due to excessive login attempts', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'attempts' => $user->login_attempts,
                ]);
            }
            
            $user->save();
        }
    }

    /**
     * Clear user's login attempts on successful login
     */
    protected function clearUserLoginAttempts($email, Request $request)
    {
        if (!$email) return;

        $user = User::where('email', strtolower($email))->first();
        if ($user) {
            $user->login_attempts = 0;
            $user->locked_until = null;
            $user->last_login_at = Carbon::now();
            $user->last_login_ip = $request->ip();
            $user->save();
        }
    }
}
