<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Rules\IsEmailDelivrable;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Model\User;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $redirectRoute = route('feed');
        if(getSetting('site.redirect_page_after_register') && getSetting('site.redirect_page_after_register') == 'settings'){
            $redirectRoute = route('my.settings');
        }
        $this->redirectTo = $redirectRoute;
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Calculate minimum birthdate for 18+
        $minBirthDate = Carbon::now()->subYears(18)->format('Y-m-d');
        
        $rules = [
            // Basic fields
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'string', 
                'min:8', 
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            
            // Account type
            'account_type' => ['required', 'in:subscriber,creator'],
            
            // Age verification (must be 18+)
            'birthdate' => ['required', 'date', 'before_or_equal:' . $minBirthDate],
            
            // Country
            'country_id' => ['required', 'exists:countries,id'],
            
            // Legal agreements - all required
            'age_confirm' => ['required', 'accepted'],
            'terms' => ['required', 'accepted'],
            'privacy' => ['required', 'accepted'],
            'community_guidelines' => ['required', 'accepted'],
            'data_processing_consent' => ['required', 'accepted'],
            
            // Optional
            'referral_code' => ['nullable', 'string', 'max:50'],
            'marketing_consent' => ['nullable', 'boolean'],
        ];
        
        // Add creator-specific validation if account type is creator
        if (isset($data['account_type']) && $data['account_type'] === 'creator') {
            $rules['creator_terms'] = ['required', 'accepted'];
            $rules['content_rights'] = ['required', 'accepted'];
        }
        
        // Add reCAPTCHA validation if enabled
        if (getSetting('security.recaptcha_enabled')) {
            $rules['g-recaptcha-response'] = ['required', 'captcha'];
        }

        $messages = [
            'name.required' => __('Please enter your full name.'),
            'username.required' => __('Please choose a username.'),
            'username.unique' => __('This username is already taken.'),
            'username.regex' => __('Username can only contain letters, numbers, and underscores.'),
            'username.min' => __('Username must be at least 3 characters.'),
            'email.required' => __('Please enter your email address.'),
            'email.email' => __('Please enter a valid email address.'),
            'email.unique' => __('This email is already registered.'),
            'password.required' => __('Please create a password.'),
            'password.min' => __('Password must be at least 8 characters.'),
            'password.confirmed' => __('Passwords do not match.'),
            'password.regex' => __('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'),
            'account_type.required' => __('Please select an account type.'),
            'account_type.in' => __('Invalid account type selected.'),
            'birthdate.required' => __('Please enter your date of birth.'),
            'birthdate.before_or_equal' => __('You must be at least 18 years old to register.'),
            'country_id.required' => __('Please select your country.'),
            'country_id.exists' => __('Please select a valid country.'),
            'age_confirm.required' => __('You must confirm that you are 18 years or older.'),
            'age_confirm.accepted' => __('You must confirm that you are 18 years or older.'),
            'terms.required' => __('You must agree to the Terms of Service.'),
            'terms.accepted' => __('You must agree to the Terms of Service.'),
            'privacy.required' => __('You must agree to the Privacy Policy.'),
            'privacy.accepted' => __('You must agree to the Privacy Policy.'),
            'community_guidelines.required' => __('You must agree to the Community Guidelines.'),
            'community_guidelines.accepted' => __('You must agree to the Community Guidelines.'),
            'data_processing_consent.required' => __('You must consent to data processing.'),
            'data_processing_consent.accepted' => __('You must consent to data processing.'),
            'creator_terms.required' => __('Creators must agree to the Creator Terms.'),
            'creator_terms.accepted' => __('Creators must agree to the Creator Terms.'),
            'content_rights.required' => __('Creators must confirm content rights ownership.'),
            'content_rights.accepted' => __('Creators must confirm content rights ownership.'),
            'g-recaptcha-response.required' => __('Please complete the captcha verification.'),
            'g-recaptcha-response.captcha' => __('Captcha verification failed. Please try again.'),
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
    
      
        // Generate username if not provided
        if (!isset($data['username']) || empty($data['username'])) {
            $data['username'] = str_replace(' ', '', strtolower($data['name'])) . rand(1000, 9999);
        }
        
        $now = Carbon::now();
        
        // Prepare user data
        $userData = [
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'birthdate' => $data['birthdate'],
            'country_id' => $data['country_id'],
            'account_type' => $data['account_type'] ?? 'subscriber',
            
            // Consent timestamps
            'terms_accepted_at' => $now,
            'privacy_accepted_at' => $now,
            'community_guidelines_accepted_at' => $now,
            'data_processing_consent_at' => $now,
            'age_verified_at' => $now,
            'age_verification_method' => 'self_declared',
            
            // Marketing consent (optional)
            'marketing_consent' => isset($data['marketing_consent']) && $data['marketing_consent'] ? true : false,
            'marketing_consent_at' => isset($data['marketing_consent']) && $data['marketing_consent'] ? $now : null,
            
            // Security tracking
            'registration_ip' => request()->ip(),
            'last_login_at' => $now,
            'last_login_ip' => request()->ip(),
            
            // Referral code
            'referral_code' => $data['referral_code'] ?? null,
            
            // KYC default status
            'kyc_status' => 'none',
            'kyc_level' => 0,
        ];
        
        // Add creator-specific consent if applicable
        if (isset($data['account_type']) && $data['account_type'] === 'creator') {
            $userData['creator_terms_accepted_at'] = $now;
            $userData['content_rights_acknowledged_at'] = $now;
        }
        
        // Email verification setting
        if (getSetting('site.enable_email_verification')) {
            // Leave email_verified_at null - user will verify via email
        } else {
            $userData['email_verified_at'] = $now;
        }
        
        try {
            $user = User::create($userData);
            
            // Log registration for audit
            Log::channel('daily')->info('New user registered', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'account_type' => $user->account_type,
                'country_id' => $user->country_id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return $user;
            
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? 'unknown',
                'ip' => request()->ip(),
            ]);
            
            throw $e;
        }
    }

    /**
     * The user has been registered.
     *
     * @param  Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Send welcome notification
        // $user->notify(new WelcomeNotification());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => __('Registration successful! Please check your email to verify your account.'),
                'redirect' => $this->redirectTo
            ]);
        }
    }
}
