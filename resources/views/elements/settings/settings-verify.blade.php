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

<form class="verify-form" action="{{route('my.settings.verify.save')}}" method="POST">
    @csrf
    <p>{{__('In order to get verified and receive your badge, please take care of the following steps:')}}</p>
    <div class="d-flex align-items-center mb-1 ml-4">
        @if(Auth::user()->email_verified_at)
            @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success mr-2'])
        @else
            @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-warning mr-2'])
        @endif
        {{__('Confirm your email address.')}}
    </div>
    <div class="d-flex align-items-center mb-1 ml-4">
        @if(Auth::user()->birthdate)
            @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success mr-2'])
        @else
            @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-warning mr-2'])
        @endif
        {{__('Set your birthdate.')}}
    </div>
    <div class="d-flex align-items-center ml-4">
        @if((Auth::user()->verification && Auth::user()->verification->status == 'verified'))
            @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success mr-2']) {{__('Upload a Goverment issued ID card.')}}
        @else
            @if(!Auth::user()->verification || (Auth::user()->verification && Auth::user()->verification->status !== 'verified' && Auth::user()->verification->status !== 'pending'))
                @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-warning mr-2']) {{__('Upload a Goverment issued ID card.')}}
            @else
                @include('elements.icon',['icon'=>'time-outline','variant'=>'medium', 'classes'=>'text-primary mr-2']) {{__('Identity check in progress.')}}
            @endif
        @endif
    </div>
    @if((!Auth::user()->verification || (Auth::user()->verification && Auth::user()->verification->status !== 'verified' && Auth::user()->verification->status !== 'pending')) )
        <h5 class="mt-5 mb-3">{{__("Complete your verification")}}</h5>
        <p class="mb-1 mt-2">{{__("Please attach clear photos of your ID card back and front side.")}}</p>
        <div class="dropzone-previews dropzone w-100 ppl-0 pr-0 pt-1 pb-1 border rounded"></div>
        <small class="form-text text-muted mb-2">{{__("Allowed file types")}}: {{str_replace(',',', ',AttachmentHelper::filterExtensions('manualPayments'))}}. {{__("Max size")}}: 4 {{__("MB")}}.</small>
        <div class="d-flex flex-row-reverse">
            <button class="btn btn-primary mt-2">{{__("Submit")}}</button>
        </div>
    @endif

    {{-- Verification Status Information Box --}}
    @if(Auth::user()->verification && Auth::user()->verification->status == 'pending')
        <div class="card border-primary mt-4" style="background: linear-gradient(135deg, rgba(0,123,255,0.1) 0%, rgba(0,123,255,0.05) 100%);">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-clock text-primary mr-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0 text-primary"><strong>Verification Status: Pending Review</strong></h5>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-calendar-check text-primary mr-2 mt-1"></i>
                            <div>
                                <strong>Submitted On:</strong><br>
                                <span class="text-muted">{{ Auth::user()->verification->created_at->format('F d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-hourglass-half text-primary mr-2 mt-1"></i>
                            <div>
                                <strong>Estimated Processing Time:</strong><br>
                                <span class="text-muted">24-48 hours (business days)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-bell text-primary mr-2 mt-1"></i>
                            <div>
                                <strong>Notification:</strong><br>
                                <span class="text-muted">You'll receive an email when your verification is processed</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt text-primary mr-2 mt-1"></i>
                            <div>
                                <strong>Check Status:</strong><br>
                                <span class="text-muted">This page (Settings → Verification)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mb-0 mt-3" style="background: rgba(0,123,255,0.1); border-color: rgba(0,123,255,0.3);">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>When can you post?</strong> Once your verification is approved, you'll be able to publish posts immediately. You can check back here anytime to see your verification status.
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->email_verified_at && Auth::user()->birthdate && (Auth::user()->verification && Auth::user()->verification->status == 'verified'))
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
