<form method="POST" action="{{ route('login') }}" style="margin-bottom: 24px;">
    @csrf
    
    @if(session('message'))
        <div style="background: rgba(236, 72, 153, 0.2); border: 1px solid rgba(236, 72, 153, 0.5); color: #fbcfe8; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px;">
            {{ session('message') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.5); color: #fecaca; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px;">
            <ul style="list-style: disc; list-style-position: inside; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <label for="email" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Email Address') }}
        </label>
        <input 
            id="email" 
            type="email" 
            name="email" 
            value="{{ old('email') }}" 
            autocomplete="email" 
            autofocus
            required
            style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
            onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
            onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
            placeholder="Enter your email"
            oninput="this.style.color='#ffffff';"
        >
        @error('email')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <div style="margin-bottom: 20px;">
        <label for="password" style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
            {{ __('Password') }}
        </label>
        <input 
            id="password" 
            type="password" 
            name="password" 
            autocomplete="current-password"
            required
            style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; font-size: 14px; transition: all 0.3s ease; box-sizing: border-box;"
            onfocus="this.style.outline='none'; this.style.borderColor='#830866'; this.style.boxShadow='0 0 0 3px rgba(131, 8, 102, 0.1)';"
            onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.boxShadow='none';"
            placeholder="Enter your password"
        >
        @error('password')
            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
        @enderror
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div style="display: flex; align-items: center;">
            <input 
                id="remember" 
                name="remember" 
                type="checkbox" 
                style="width: 16px; height: 16px; margin-right: 8px; cursor: pointer;"
            >
            <label for="remember" style="display: block; font-size: 14px; color: #d1d5db; margin: 0; cursor: pointer;">
                {{ __('Remember me') }}
            </label>
        </div>
        @if (Route::has('password.request'))
            <div style="font-size: 14px;">
                <a href="{{ route('password.request') }}" style="color: #830866; font-weight: 500; text-decoration: none;">
                    {{ __('Forgot Password?') }}
                </a>
            </div>
        @endif
    </div>

    <button 
        type="submit" 
        style="width: 100%; background: linear-gradient(135deg, #830866 0%, #a10a7f 100%); color: #ffffff; font-weight: 600; padding: 12px 16px; border-radius: 12px; border: none; font-size: 14px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(131, 8, 102, 0.3);"
        onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 20px rgba(131, 8, 102, 0.4)';"
        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(131, 8, 102, 0.3)';"
        onmousedown="this.style.transform='scale(0.98)';"
        onmouseup="this.style.transform='scale(1.02)';"
    >
        {{ __('Sign In') }}
    </button>
</form>
