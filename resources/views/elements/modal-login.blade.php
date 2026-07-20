{{--
    Global authentication modal (Login / Sign up / Forgot password).
    Included once globally for guests in layouts/generic.blade.php.
    Every `data-toggle="modal" data-target="#login-dialog"` trigger opens this.
    Tabs are switched via LoginModal.changeActiveTab('login'|'register'|'forgot').
--}}
<div class="modal fade auth-modal" tabindex="-1" role="dialog" id="login-dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered auth-modal-dialog" role="document">
        <div class="modal-content auth-modal-content">

            <button type="button" class="auth-modal-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>

            {{-- Logo --}}
            <div class="auth-modal-logo">
                <img src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}" alt="{{getSetting('site.name')}}">
            </div>

            {{-- Tabs --}}
            <div class="auth-modal-tabs" role="tablist">
                <button type="button" class="auth-modal-tab active" data-auth-tab="login"
                        onclick="LoginModal.changeActiveTab('login')">{{__('Log in')}}</button>
                <button type="button" class="auth-modal-tab" data-auth-tab="register"
                        onclick="LoginModal.changeActiveTab('register')">{{__('Sign up')}}</button>
            </div>

            <div class="modal-body auth-modal-body">
                @include('auth.modal-forms')
            </div>

        </div>
    </div>
</div>

<style>
    .auth-modal .auth-modal-dialog {
        max-width: 460px;
    }
    .auth-modal .auth-modal-content {
        position: relative;
        background: linear-gradient(to bottom right, #0d0d0d, #1a1a1a, #0d0d0d);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.55);
        padding: 28px 26px 24px;
        color: #fff;
        font-family: 'Inter', sans-serif;
    }
    .auth-modal .auth-modal-close {
        position: absolute;
        top: 14px;
        right: 16px;
        background: rgba(255, 255, 255, 0.08);
        border: none;
        color: #cbd5e1;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 15px;
        transition: all 0.2s ease;
        z-index: 2;
    }
    .auth-modal .auth-modal-close:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
    }
    .auth-modal .auth-modal-logo {
        text-align: center;
        margin: 4px 0 18px;
    }
    .auth-modal .auth-modal-logo img {
        height: 42px;
        max-width: 70%;
        object-fit: contain;
    }
    .auth-modal .auth-modal-tabs {
        display: flex;
        gap: 8px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 5px;
        margin-bottom: 22px;
    }
    .auth-modal .auth-modal-tab {
        flex: 1;
        background: transparent;
        border: none;
        color: #9ca3af;
        font-size: 15px;
        font-weight: 600;
        padding: 10px 0;
        border-radius: 9px;
        cursor: pointer;
        transition: all 0.25s ease;
        font-family: inherit;
    }
    .auth-modal .auth-modal-tab:hover {
        color: #fff;
    }
    .auth-modal .auth-modal-tab.active {
        background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(131, 8, 102, 0.35);
    }
    .auth-modal .auth-modal-body {
        padding: 0;
        max-height: 72vh;
        overflow-y: auto;
    }
    /* Slimmer scrollbar inside the modal */
    .auth-modal .auth-modal-body::-webkit-scrollbar { width: 6px; }
    .auth-modal .auth-modal-body::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.18);
        border-radius: 3px;
    }
    /* On phones the modal becomes a near full-screen sheet (per design decision) */
    @media (max-width: 576px) {
        .auth-modal .auth-modal-dialog {
            max-width: 100%;
            margin: 0;
            min-height: 100%;
            align-items: stretch;
        }
        .auth-modal .auth-modal-content {
            border-radius: 0;
            min-height: 100vh;
        }
        .auth-modal .auth-modal-body {
            max-height: none;
        }
    }
</style>
