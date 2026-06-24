<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'remember'));
        }

        // Check if user exists and is admin
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Invalid email or password.');
        }
        
        // Check if user has admin role (role_id = 1 for admin based on your table)
        if ($user->role_id != 1) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'You do not have permission to access the admin panel.');
        }

        // Attempt to authenticate
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Authentication successful
            $user = Auth::user();
            
            // Regenerate session
            $request->session()->regenerate();
            
            // Set admin session flag
            session(['is_admin' => true]);
            
            return redirect()->intended(route('voyager.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Authentication failed
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'Invalid email or password. Please try again.');
    }

    /**
     * Handle admin logout.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Remove admin session flag
        session()->forget('is_admin');
        
        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
