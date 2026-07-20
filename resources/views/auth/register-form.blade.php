<form method="POST" action="{{ route('register') }}" id="register-form" style="margin-bottom: 24px;">
    @csrf

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.5); color: #fecaca; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px;">
            <ul style="list-style: disc; list-style-position: inside; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Account Type Selection: I want to * -->
    <div class="register-account-type-wrap">
        <label class="register-account-type-label">
            {{ __('I want to') }} <span class="required-star">*</span>
        </label>
        <div class="register-account-type-buttons">
            <label class="register-account-type-btn account-type-option selected" data-type="subscriber">
                <input type="radio" name="account_type" value="subscriber" checked style="display: none;">
                <div class="btn-radio">
                    <span class="dot"></span>
                </div>
                <div class="btn-text">
                    <div class="title">{{ __('Subscribe & Watch') }}</div>
                    <div class="subtitle">{{ __('Fan/Subscriber account') }}</div>
                </div>
            </label>
            <label class="register-account-type-btn account-type-option" data-type="creator">
                <input type="radio" name="account_type" value="creator" style="display: none;">
                <div class="btn-radio">
                    <span class="dot"></span>
                </div>
                <div class="btn-text">
                    <div class="title">{{ __('Create & Earn') }}</div>
                    <div class="subtitle">{{ __('Content Creator account') }}</div>
                </div>
            </label>
        </div>
        @error('account_type')
            <p style="margin-top: 8px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Full Name -->
    <div style="margin-bottom: 20px;">
        <label for="name" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Full Name') }} <span style="color: #ff6b6b;">*</span>
        </label>
        <input 
            id="name" 
            type="text" 
            name="name" 
            value="{{ old('name') }}"
            autocomplete="name"
            @if(($mode ?? '') !== 'ajax') autofocus @endif
            required
            style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
            onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
            onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
            placeholder="Enter your full name"
        >
        @error('name')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Username -->
    <div style="margin-bottom: 20px;">
        <label for="username" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Username') }} <span style="color: #ff6b6b;">*</span>
        </label>
        <div style="position: relative;">
            <input 
                id="username" 
                type="text" 
                name="username" 
                value="{{ old('username') }}" 
                required 
                autocomplete="username"
                style="width: 100%; padding: 12px 16px; padding-right: 40px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
                onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
                onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
                placeholder="Choose a username"
                oninput="checkUsernameAvailability(this.value)"
            >
            <span id="username-status" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 18px;"></span>
        </div>
        <p id="username-feedback" style="margin-top: 4px; font-size: 12px; color: #9ca3af;"></p>
        @error('username')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email -->
    <div style="margin-bottom: 20px;">
        <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Email Address') }} <span style="color: #ff6b6b;">*</span>
        </label>
        <input 
            id="email" 
            type="email" 
            name="email" 
            value="{{ old('email') }}" 
            required 
            autocomplete="email"
            style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
            onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
            onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
            placeholder="Enter your email"
        >
        @error('email')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Date of Birth -->
    <div style="margin-bottom: 20px;">
        <label for="birthdate" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Date of Birth') }} <span style="color: #ff6b6b;">*</span>
            <span style="font-size: 12px; color: #9ca3af; margin-left: 8px;">({{ __('Must be 18+') }})</span>
        </label>
        <input 
            id="birthdate" 
            type="date" 
            name="birthdate" 
            value="{{ old('birthdate') }}" 
            required 
            max="{{ \Carbon\Carbon::now()->subYears(18)->format('Y-m-d') }}"
            style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
            onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
            onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none'; validateAge(this.value);"
        >
        <p id="age-feedback" style="margin-top: 4px; font-size: 12px; color: #9ca3af;"></p>
        @error('birthdate')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Country -->
    <div style="margin-bottom: 20px;">
        <label for="country_id" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Country/Region') }} <span style="color: #ff6b6b;">*</span>
        </label>
        <select 
            id="country_id" 
            name="country_id" 
            required
            style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box; appearance: none; cursor: pointer;"
            onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
            onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
        >
            <option value="" style="background: #2d2d2d; color: #ffffff;">{{ __('Select your country') }}</option>
            @php
                try {
                    // Get United States first
                    $unitedStates = \App\Model\Country::where('name', 'United States of America (the)')->first();
                    
                    // Get all other countries (excluding "All" and United States)
                    $otherCountries = \App\Model\Country::where('name', '!=', 'All')
                        ->where('name', '!=', 'United States of America (the)')
                        ->orderBy('name')
                        ->get();
                    
                    // If countries don't exist, seed them
                    if ($otherCountries->isEmpty() && !$unitedStates) {
                        \Artisan::call('db:seed', ['--class' => 'InsertCountries']);
                        $unitedStates = \App\Model\Country::where('name', 'United States of America (the)')->first();
                        $otherCountries = \App\Model\Country::where('name', '!=', 'All')
                            ->where('name', '!=', 'United States of America (the)')
                            ->orderBy('name')
                            ->get();
                    }
                    
                    // Combine: United States first, then others
                    $countries = collect([]);
                    if ($unitedStates) {
                        $countries->push($unitedStates);
                    }
                    $countries = $countries->merge($otherCountries);
                    
                    // Set default country ID for United States
                    $defaultCountryId = $unitedStates ? $unitedStates->id : null;
                } catch (\Exception $e) {
                    $countries = collect([]);
                    $defaultCountryId = null;
                }
            @endphp
            @if($countries->isNotEmpty())
                @foreach($countries as $country)
                    <option 
                        value="{{ $country->id }}" 
                        style="background: #2d2d2d; color: #ffffff; padding: 8px;" 
                        {{ (old('country_id') ? old('country_id') == $country->id : ($defaultCountryId && $country->id == $defaultCountryId)) ? 'selected' : '' }}
                    >
                        {{ $country->name }}
                    </option>
                @endforeach
            @else
                <option value="" style="background: #2d2d2d; color: #ffffff;">{{ __('Countries loading...') }}</option>
            @endif
        </select>
        @error('country_id')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div style="margin-bottom: 20px;">
        <label for="password" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Password') }} <span style="color: #ff6b6b;">*</span>
        </label>
        <div style="position: relative;">
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password"
                minlength="8"
                style="width: 100%; padding: 12px 16px; padding-right: 50px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
                onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
                onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
                placeholder="Create a password (min 8 characters)"
                oninput="checkPasswordStrength(this.value)"
            >
            <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                <i class="fas fa-eye" id="password-toggle-icon"></i>
            </button>
        </div>
        <!-- Password Strength Indicator -->
        <div style="margin-top: 8px;">
            <div style="display: flex; gap: 4px; margin-bottom: 4px;">
                <div id="strength-1" style="flex: 1; height: 4px; background: rgba(255, 255, 255, 0.2); border-radius: 2px; transition: background 0.3s;"></div>
                <div id="strength-2" style="flex: 1; height: 4px; background: rgba(255, 255, 255, 0.2); border-radius: 2px; transition: background 0.3s;"></div>
                <div id="strength-3" style="flex: 1; height: 4px; background: rgba(255, 255, 255, 0.2); border-radius: 2px; transition: background 0.3s;"></div>
                <div id="strength-4" style="flex: 1; height: 4px; background: rgba(255, 255, 255, 0.2); border-radius: 2px; transition: background 0.3s;"></div>
            </div>
            <p id="password-strength-text" style="font-size: 12px; color: #9ca3af; margin: 0;"></p>
        </div>
        <ul style="margin-top: 8px; padding-left: 20px; font-size: 12px; color: #9ca3af;">
            <li id="req-length" style="margin-bottom: 2px;">{{ __('At least 8 characters') }}</li>
            <li id="req-upper" style="margin-bottom: 2px;">{{ __('One uppercase letter') }}</li>
            <li id="req-lower" style="margin-bottom: 2px;">{{ __('One lowercase letter') }}</li>
            <li id="req-number" style="margin-bottom: 2px;">{{ __('One number') }}</li>
            <li id="req-special">{{ __('One special character (!@#$%^&*)') }}</li>
        </ul>
        @error('password')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div style="margin-bottom: 20px;">
        <label for="password-confirm" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Confirm Password') }} <span style="color: #ff6b6b;">*</span>
        </label>
        <div style="position: relative;">
            <input 
                id="password-confirm" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                style="width: 100%; padding: 12px 16px; padding-right: 50px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
                onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
                onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
                placeholder="Confirm your password"
                oninput="checkPasswordMatch()"
            >
            <button type="button" onclick="togglePassword('password-confirm')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                <i class="fas fa-eye" id="password-confirm-toggle-icon"></i>
            </button>
        </div>
        <p id="password-match-feedback" style="margin-top: 4px; font-size: 12px;"></p>
        @error('password_confirmation')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <!-- Referral Code (Optional) -->
    <div style="margin-bottom: 20px;">
        <label for="referral_code" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Referral Code') }} <span style="font-size: 12px; color: #9ca3af;">({{ __('Optional') }})</span>
        </label>
        <input 
            id="referral_code" 
            type="text" 
            name="referral_code" 
            value="{{ old('referral_code', request('ref')) }}" 
            autocomplete="off"
            style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
            onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
            onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
            placeholder="Enter referral code if you have one"
        >
    </div>

    <!-- Legal Agreements Section -->
    <div style="background: rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 16px; margin-bottom: 20px;">
        <p style="font-size: 14px; font-weight: 600; color: #d1d5db; margin-bottom: 16px;">
            <i class="fas fa-shield-alt" style="margin-right: 8px; color: #830866;"></i>
            {{ __('Legal Agreements') }}
        </p>
        
        <!-- Age Confirmation -->
        <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="ageConfirm" 
                    name="age_confirm" 
                    type="checkbox" 
                    value="1" 
                    required
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="ageConfirm" style="font-size: 14px; color: #d1d5db; line-height: 1.5; cursor: pointer;">
                <span style="color: #ff6b6b;">*</span> {{ __('I confirm that I am 18 years of age or older') }}
            </label>
        </div>
        
        <!-- Terms of Service -->
        <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="tosAgree" 
                    name="terms" 
                    type="checkbox" 
                    value="1" 
                    required
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="tosAgree" style="font-size: 14px; color: #d1d5db; line-height: 1.5; cursor: pointer;">
                <span style="color: #ff6b6b;">*</span> {{ __('I agree to the') }} <a href="{{route('pages.get',['slug'=>GenericHelper::getTOSPage()->slug])}}" target="_blank" style="color: #830866; text-decoration: none;">{{ __('Terms of Service') }}</a>
            </label>
        </div>
        
        <!-- Privacy Policy -->
        <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="privacyAgree" 
                    name="privacy" 
                    type="checkbox" 
                    value="1" 
                    required
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="privacyAgree" style="font-size: 14px; color: #d1d5db; line-height: 1.5; cursor: pointer;">
                <span style="color: #ff6b6b;">*</span> {{ __('I agree to the') }} <a href="{{ route('pages.get', ['slug' => 'privacy-policy']) }}" target="_blank" style="color: #830866; text-decoration: none;">{{ __('Privacy Policy') }}</a>
            </label>
        </div>
        
        <!-- Community Guidelines -->
        <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="guidelinesAgree" 
                    name="community_guidelines" 
                    type="checkbox" 
                    value="1" 
                    required
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="guidelinesAgree" style="font-size: 14px; color: #d1d5db; line-height: 1.5; cursor: pointer;">
                <span style="color: #ff6b6b;">*</span> {{ __('I agree to the') }} <a href="{{ route('pages.get', ['slug' => 'community-guidelines']) }}" target="_blank" style="color: #830866; text-decoration: none;">{{ __('Community Guidelines') }}</a>
            </label>
        </div>
        
        <!-- Data Processing Consent (GDPR) -->
        <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="dataConsent" 
                    name="data_processing_consent" 
                    type="checkbox" 
                    value="1" 
                    required
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="dataConsent" style="font-size: 14px; color: #d1d5db; line-height: 1.5; cursor: pointer;">
                <span style="color: #ff6b6b;">*</span> {{ __('I consent to the processing of my personal data as described in the Privacy Policy') }}
            </label>
        </div>
        
        <!-- Marketing Consent (Optional) -->
        <div style="display: flex; align-items: flex-start; margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="marketingConsent" 
                    name="marketing_consent" 
                    type="checkbox" 
                    value="1"
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="marketingConsent" style="font-size: 14px; color: #9ca3af; line-height: 1.5; cursor: pointer;">
                {{ __('I would like to receive promotional emails and updates (optional)') }}
            </label>
        </div>
    </div>

    <!-- Creator-specific terms (shown when creator is selected) -->
    <div id="creator-terms" style="display: none; background: rgba(131, 8, 102, 0.1); border: 1px solid rgba(131, 8, 102, 0.3); border-radius: 12px; padding: 16px; margin-bottom: 20px;">
        <p style="font-size: 14px; font-weight: 600; color: #d1d5db; margin-bottom: 16px;">
            <i class="fas fa-star" style="margin-right: 8px; color: #830866;"></i>
            {{ __('Creator Agreement') }}
        </p>
        
        <!-- Creator Terms -->
        <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="creatorTerms" 
                    name="creator_terms" 
                    type="checkbox" 
                    value="1"
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="creatorTerms" style="font-size: 14px; color: #d1d5db; line-height: 1.5; cursor: pointer;">
                <span style="color: #ff6b6b;">*</span> {{ __('I agree to the') }} <a href="{{ route('pages.get', ['slug' => 'creator-terms']) }}" target="_blank" style="color: #830866; text-decoration: none;">{{ __('Creator Terms & Conditions') }}</a>
            </label>
        </div>
        
        <!-- Content Rights -->
        <div style="display: flex; align-items: flex-start;">
            <div style="display: flex; align-items: center; height: 20px; margin-right: 12px; margin-top: 2px;">
                <input 
                    id="contentRights" 
                    name="content_rights" 
                    type="checkbox" 
                    value="1"
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #830866;"
                >
            </div>
            <label for="contentRights" style="font-size: 14px; color: #d1d5db; line-height: 1.5; cursor: pointer;">
                <span style="color: #ff6b6b;">*</span> {{ __('I confirm that I own or have the rights to all content I will upload') }}
            </label>
        </div>
        
        <p style="font-size: 12px; color: #9ca3af; margin-top: 12px;">
            <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
            {{ __('As a creator, you will need to complete identity verification (ID check) before you can start posting content and earning.') }}
        </p>
    </div>

    @if(getSetting('security.recaptcha_enabled') && !Auth::check())
        <div style="display: flex; justify-content: center; margin-bottom: 24px;">
            {!! NoCaptcha::display(['data-theme' => (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme')) : Cookie::get('app_theme') )]) !!}
            @error('g-recaptcha-response')
                <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{__("Please check the captcha field.")}}</p>
            @enderror
        </div>
    @endif

    <button 
        type="submit" 
        id="submit-btn"
        style="width: 100%; background: linear-gradient(135deg, #830866 0%, #a10a7f 100%); color: #ffffff; font-weight: 600; padding: 14px 16px; border-radius: 12px; border: none; font-size: 16px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(131, 8, 102, 0.3); margin-top: 24px; display: flex; align-items: center; justify-content: center; gap: 8px;"
        onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 20px rgba(131, 8, 102, 0.4)';"
        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(131, 8, 102, 0.3)';"
        onmousedown="this.style.transform='scale(0.98)';"
        onmouseup="this.style.transform='scale(1.02)';"
    >
        <i class="fas fa-user-plus"></i>
        {{ __('Create Account') }}
    </button>
    
    <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 16px;">
        <i class="fas fa-lock" style="margin-right: 4px;"></i>
        {{ __('Your data is protected with industry-standard encryption') }}
    </p>
</form>

<script>
    // Account type selection (button-style)
    document.querySelectorAll('.account-type-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.account-type-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
            this.querySelector('input').checked = true;

            const creatorTerms = document.getElementById('creator-terms');
            if (this.dataset.type === 'creator') {
                creatorTerms.style.display = 'block';
                document.getElementById('creatorTerms').required = true;
                document.getElementById('contentRights').required = true;
            } else {
                creatorTerms.style.display = 'none';
                document.getElementById('creatorTerms').required = false;
                document.getElementById('contentRights').required = false;
            }
        });
    });

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-toggle-icon');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        const requirements = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };
        
        // Update requirement indicators
        document.getElementById('req-length').style.color = requirements.length ? '#22c55e' : '#9ca3af';
        document.getElementById('req-upper').style.color = requirements.upper ? '#22c55e' : '#9ca3af';
        document.getElementById('req-lower').style.color = requirements.lower ? '#22c55e' : '#9ca3af';
        document.getElementById('req-number').style.color = requirements.number ? '#22c55e' : '#9ca3af';
        document.getElementById('req-special').style.color = requirements.special ? '#22c55e' : '#9ca3af';
        
        // Calculate strength
        strength = Object.values(requirements).filter(Boolean).length;
        
        // Update strength bars
        const colors = { 1: '#ef4444', 2: '#f97316', 3: '#eab308', 4: '#22c55e', 5: '#22c55e' };
        const texts = { 0: '', 1: '{{ __("Very Weak") }}', 2: '{{ __("Weak") }}', 3: '{{ __("Fair") }}', 4: '{{ __("Strong") }}', 5: '{{ __("Very Strong") }}' };
        
        for (let i = 1; i <= 4; i++) {
            document.getElementById('strength-' + i).style.background = i <= Math.min(strength, 4) ? colors[strength] : 'rgba(255, 255, 255, 0.2)';
        }
        
        document.getElementById('password-strength-text').textContent = texts[strength];
        document.getElementById('password-strength-text').style.color = colors[strength] || '#9ca3af';
    }
    
    // Check password match
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password-confirm').value;
        const feedback = document.getElementById('password-match-feedback');
        
        if (confirm.length === 0) {
            feedback.textContent = '';
            return;
        }
        
        if (password === confirm) {
            feedback.textContent = '✓ {{ __("Passwords match") }}';
            feedback.style.color = '#22c55e';
        } else {
            feedback.textContent = '✗ {{ __("Passwords do not match") }}';
            feedback.style.color = '#ef4444';
        }
    }
    
    // Age validation
    function validateAge(dateString) {
        const feedback = document.getElementById('age-feedback');
        if (!dateString) {
            feedback.textContent = '';
            return;
        }
        
        const birthDate = new Date(dateString);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        if (age >= 18) {
            feedback.textContent = '✓ {{ __("Age verified") }} (' + age + ' {{ __("years old") }})';
            feedback.style.color = '#22c55e';
        } else {
            feedback.textContent = '✗ {{ __("You must be 18 or older to register") }}';
            feedback.style.color = '#ef4444';
        }
    }
    
    // Username availability check
    let usernameTimeout;
    function checkUsernameAvailability(username) {
        clearTimeout(usernameTimeout);
        const status = document.getElementById('username-status');
        const feedback = document.getElementById('username-feedback');
        
        if (username.length < 3) {
            status.textContent = '';
            feedback.textContent = username.length > 0 ? '{{ __("Username must be at least 3 characters") }}' : '';
            feedback.style.color = '#9ca3af';
            return;
        }
        
        status.innerHTML = '<i class="fas fa-spinner fa-spin" style="color: #9ca3af;"></i>';
        feedback.textContent = '{{ __("Checking availability...") }}';
        
        usernameTimeout = setTimeout(() => {
            fetch('/api/check-username?username=' + encodeURIComponent(username))
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        status.innerHTML = '<i class="fas fa-check-circle" style="color: #22c55e;"></i>';
                        feedback.textContent = '{{ __("Username is available") }}';
                        feedback.style.color = '#22c55e';
                    } else {
                        status.innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i>';
                        feedback.textContent = '{{ __("Username is already taken") }}';
                        feedback.style.color = '#ef4444';
                    }
                })
                .catch(() => {
                    status.textContent = '';
                    feedback.textContent = '';
                });
        }, 500);
    }
</script>
