<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use TCG\Voyager\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\RequestException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        // Handling 2FA stuff
        $force2FA = false;
        if(getSetting('security.enable_2fa')){
            if($user->enable_2fa && !in_array(AuthServiceProvider::generate2FaDeviceSignature(), AuthServiceProvider::getUserDevices($user->id))){
                AuthServiceProvider::generate2FACode();
                AuthServiceProvider::addNewUserDevice($user->id);
                $force2FA = true;
            }
        }
        Session::put('force2fa', $force2FA);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Logged in successfully.']);
        }

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Redirect the user to the Facebook authentication page.
     */
    public function redirectToProvider(Request $request)
    {
        return Socialite::driver($request->route('provider'))->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     */
    public function handleProviderCallback(Request $request)
    {
        $provider = $request->route('provider');

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (RequestException $e) {
            return redirect(route('login'))->with('error', __('Social login failed. Please try again.'));
        } catch (\Exception $e) {
            return redirect(route('login'))->with('error', __('Social login failed. Please try again.'));
        }

        // Check if user exists by provider ID
        $userCheck = User::where('auth_provider_id', $socialUser->id)
            ->where('auth_provider', $provider)
            ->first();
            
        if($userCheck){
            $authUser = $userCheck;
        } else {
            // Check if user exists by email (link accounts)
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if($existingUser) {
                // Link social account to existing user
                $existingUser->update([
                    'auth_provider' => $provider,
                    'auth_provider_id' => $socialUser->id,
                ]);
                $authUser = $existingUser;
            } else {
                // Create new user
                try {
                    $authUser = AuthServiceProvider::createUser([
                        'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'User',
                        'email' => $socialUser->getEmail(),
                        'auth_provider' => $provider,
                        'auth_provider_id' => $socialUser->id,
                        'email_verified_at' => now(), // Social logins are pre-verified
                    ]);
                } catch (\Exception $exception) {
                    return redirect(route('login'))->with('error', $exception->getMessage() ?: __('Failed to create account. Please try again.'));
                }
            }
        }

        Auth::login($authUser, true);
        $redirectTo = route('feed');
        if (Session::has('lastProfileUrl')) {
            $redirectTo = Session::get('lastProfileUrl');
        }
        return redirect($redirectTo)->with('success', __('Welcome! You have been logged in successfully.'));
    }
}
