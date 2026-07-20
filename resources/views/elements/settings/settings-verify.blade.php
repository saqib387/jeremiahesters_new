@if(session('success'))
    <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
        <div class="d-flex align-items-start">
            <i class="fas fa-check-circle mr-2 mt-1" style="font-size: 1.2rem;"></i>
            <div class="flex-grow-1">
                <strong>{{session('success')}}</strong>
                <div class="mt-2" style="font-size: 0.9rem; opacity: 0.95;">
                    <p class="mb-1"><i class="fas fa-info-circle mr-1"></i><strong>What happens next?</strong></p>
                    <ul class="mb-0 pl-3" style="list-style-type: disc;">
                        <li>Your verification request has been submitted successfully</li>
                        <li>Our admin team will review your ID documents</li>
                        <li>Processing typically takes <strong>24-48 hours</strong> during business days</li>
                        <li>You'll receive an email notification once your verification is processed</li>
                    </ul>
                    <p class="mb-0 mt-2"><i class="fas fa-map-marker-alt mr-1"></i><strong>Check your status:</strong> You can check your verification status on this page at any time.</p>
                    <p class="mb-0 mt-1"><i class="fas fa-edit mr-1"></i><strong>When can you post?</strong> Once your verification is approved, you'll be able to publish posts immediately!</p>
                </div>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-warning text-white font-weight-bold mt-2" role="alert">
        {{session('error')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(Auth::user()->verification && (Auth::user()->verification->rejectionReason && Auth::user()->verification->status === 'rejected' ) )
    <div class="alert alert-warning text-white font-weight-bold mt-2" role="alert">
        {{__("Your previous verification attempt was rejected for the following reason:")}} "{{Auth::user()->verification->rejectionReason}}"
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="verify-page">
<form class="verify-form" action="{{route('my.settings.verify.save')}}" method="POST">
    @csrf

    {{-- Intro --}}
    <div class="verify-hero">
        <div class="verify-hero-badge">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div>
            <h5 class="verify-hero-title">{{__('Get verified & earn')}}</h5>
            <p class="verify-hero-text mb-0">{{__('In order to get verified and receive your badge, please take care of the following steps:')}}</p>
        </div>
    </div>

    {{-- Steps checklist --}}
    <div class="verify-card">
        {{-- Email --}}
        <div class="verify-step {{ Auth::user()->email_verified_at ? 'is-done' : '' }}">
            <span class="verify-step-icon">
                @if(Auth::user()->email_verified_at)
                    @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium'])
                @else
                    @include('elements.icon',['icon'=>'mail-outline','variant'=>'medium'])
                @endif
            </span>
            <span class="verify-step-label">{{__('Confirm your email address.')}}</span>
            @if(Auth::user()->email_verified_at)
                <span class="verify-badge done">{{__('Done')}}</span>
            @else
                <span class="verify-badge todo">{{__('Required')}}</span>
            @endif
        </div>

        {{-- Birthdate --}}
        <div class="verify-step {{ Auth::user()->birthdate ? 'is-done' : '' }}">
            <span class="verify-step-icon">
                @if(Auth::user()->birthdate)
                    @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium'])
                @else
                    @include('elements.icon',['icon'=>'calendar-outline','variant'=>'medium'])
                @endif
            </span>
            <span class="verify-step-label">{{__('Set your birthdate.')}}</span>
            @if(Auth::user()->birthdate)
                <span class="verify-badge done">{{__('Done')}}</span>
            @else
                <span class="verify-badge todo">{{__('Required')}}</span>
            @endif
        </div>

        {{-- ID --}}
        @php($verification = Auth::user()->verification)
        <div class="verify-step {{ ($verification && $verification->status == 'verified') ? 'is-done' : '' }}">
            <span class="verify-step-icon">
                @if($verification && $verification->status == 'verified')
                    @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium'])
                @elseif($verification && $verification->status == 'pending')
                    @include('elements.icon',['icon'=>'time-outline','variant'=>'medium'])
                @else
                    @include('elements.icon',['icon'=>'card-outline','variant'=>'medium'])
                @endif
            </span>
            <span class="verify-step-label">
                @if($verification && $verification->status == 'pending')
                    {{__('Identity check in progress.')}}
                @else
                    {{__('Upload a Goverment issued ID card.')}}
                @endif
            </span>
            @if($verification && $verification->status == 'verified')
                <span class="verify-badge done">{{__('Done')}}</span>
            @elseif($verification && $verification->status == 'pending')
                <span class="verify-badge pending">{{__('In review')}}</span>
            @else
                <span class="verify-badge todo">{{__('Required')}}</span>
            @endif
        </div>
    </div>

    @if((!$verification || ($verification && $verification->status !== 'verified' && $verification->status !== 'pending')) )
        <div class="verify-card verify-upload-card">
            <h5 class="verify-section-title">{{__("Complete your verification")}}</h5>
            <p class="verify-upload-hint">{{__("Please attach clear photos of your ID card back and front side.")}}</p>
            <div class="dropzone-previews dropzone w-100 ppl-0 pr-0 pt-1 pb-1 border rounded"></div>
            <small class="form-text text-muted mb-2">{{__("Allowed file types")}}: {{str_replace(',',', ',AttachmentHelper::filterExtensions('manualPayments'))}}. {{__("Max size")}}: 4 {{__("MB")}}.</small>
            <button class="btn btn-primary btn-block verify-submit mt-3">{{__("Submit")}}</button>
        </div>
    @endif

    {{-- Verification Status Information Box --}}
    @if($verification && $verification->status == 'pending')
        <div class="card border-primary mt-4" style="background: linear-gradient(135deg, rgba(131,8,102,0.10) 0%, rgba(131,8,102,0.04) 100%); border-color: rgba(131,8,102,0.3) !important;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-clock mr-2" style="font-size: 1.5rem; color:#830866;"></i>
                    <h5 class="mb-0" style="color:#830866;"><strong>Verification Status: Pending Review</strong></h5>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-calendar-check mr-2 mt-1" style="color:#830866;"></i>
                            <div>
                                <strong>Submitted On:</strong><br>
                                <span class="text-muted">{{ Auth::user()->verification->created_at->format('F d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-hourglass-half mr-2 mt-1" style="color:#830866;"></i>
                            <div>
                                <strong>Estimated Processing Time:</strong><br>
                                <span class="text-muted">24-48 hours (business days)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-bell mr-2 mt-1" style="color:#830866;"></i>
                            <div>
                                <strong>Notification:</strong><br>
                                <span class="text-muted">You'll receive an email when your verification is processed</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt mr-2 mt-1" style="color:#830866;"></i>
                            <div>
                                <strong>Check Status:</strong><br>
                                <span class="text-muted">This page (Settings → Verification)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert mb-0 mt-3" style="background: rgba(131,8,102,0.08); border: 1px solid rgba(131,8,102,0.25); color:#5a0746;">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>When can you post?</strong> Once your verification is approved, you'll be able to publish posts immediately. You can check back here anytime to see your verification status.
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->email_verified_at && Auth::user()->birthdate && ($verification && $verification->status == 'verified'))
        <div class="card border-success mt-4" style="background: linear-gradient(135deg, rgba(40,167,69,0.1) 0%, rgba(40,167,69,0.05) 100%);">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                <h4 class="text-success mb-2"><strong>Verification Complete!</strong></h4>
                <p class="mb-3">{{__("Your info looks good, you're all set to post new content!")}}</p>
                <div class="d-flex justify-content-center align-items-center">
                    <i class="fas fa-edit text-success mr-2"></i>
                    <span><strong>You can now publish posts!</strong></span>
                </div>
                @if(Auth::user()->verification->updated_at)
                    <small class="text-muted mt-2 d-block">Verified on: {{ Auth::user()->verification->updated_at->format('F d, Y') }}</small>
                @endif
            </div>
        </div>
    @endif
</form>
@include('elements.uploaded-file-preview-template')
</div>

<style>
.verify-page { max-width: 640px; }
.verify-hero {
    display: flex;
    align-items: center;
    gap: 16px;
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    color: #fff;
    border-radius: 16px;
    padding: 20px 22px;
    margin-bottom: 18px;
}
.verify-hero-badge {
    flex-shrink: 0;
    width: 52px; height: 52px;
    border-radius: 14px;
    background: rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
}
.verify-hero-title { margin: 0 0 4px; font-weight: 700; color:#fff; }
.verify-hero-text { font-size: 14px; opacity: 0.92; line-height: 1.5; }

.verify-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.07);
    border-radius: 16px;
    padding: 8px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    margin-bottom: 18px;
}
.verify-step {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid rgba(0,0,0,0.06);
}
.verify-step:last-child { border-bottom: none; }
.verify-step-icon {
    flex-shrink: 0;
    width: 38px; height: 38px;
    border-radius: 10px;
    background: rgba(131,8,102,0.08);
    color: #830866;
    display: flex; align-items: center; justify-content: center;
}
.verify-step.is-done .verify-step-icon { background: rgba(40,167,69,0.12); color: #28a745; }
.verify-step-label { flex: 1; font-weight: 500; font-size: 15px; }
.verify-badge {
    flex-shrink: 0;
    font-size: 12px; font-weight: 600;
    padding: 4px 12px; border-radius: 20px;
    white-space: nowrap;
}
.verify-badge.done { background: rgba(40,167,69,0.12); color: #1e7e34; }
.verify-badge.todo { background: rgba(131,8,102,0.10); color: #830866; }
.verify-badge.pending { background: rgba(255,159,0,0.15); color: #b06f00; }

.verify-section-title { font-weight: 700; margin: 4px 0 6px; }
.verify-upload-hint { font-size: 14px; color: #555; margin-bottom: 12px; }
.verify-page .dropzone {
    border: 2px dashed rgba(131,8,102,0.35) !important;
    border-radius: 14px !important;
    background: rgba(131,8,102,0.03);
    min-height: 150px;
    display: flex; align-items: center; justify-content: center;
    text-align: center;
    transition: all 0.2s ease;
    cursor: pointer;
}
.verify-page .dropzone:hover {
    border-color: #830866 !important;
    background: rgba(131,8,102,0.06);
}
.verify-submit {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    border: none;
    font-weight: 600;
    padding: 12px;
    border-radius: 12px;
}
.verify-submit:hover { filter: brightness(1.05); }
</style>
