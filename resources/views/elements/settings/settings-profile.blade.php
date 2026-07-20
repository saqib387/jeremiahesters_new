@if(!Auth::user()->email_verified_at) @include('elements.resend-verification-email-box') @endif

@if(getSetting('ai.open_ai_enabled'))
    @include('elements.suggest-description')
@endif

<div class="profile-settings">
    <form method="POST" action="{{ route('my.settings.profile.save', ['type' => 'profile']) }}" class="profile-settings__form">
        @csrf
        @include('elements.dropzone-dummy-element')

        <div class="profile-settings__media">
            <div class="profile-settings__cover-wrap">
                <div class="card profile-cover-bg profile-settings__cover">
                    <img class="card-img-top centered-and-cropped profile-settings__cover-img" src="{{ Auth::user()->cover }}" alt="">
                    <div class="card-img-overlay profile-settings__cover-overlay d-flex justify-content-center align-items-center">
                        <div class="actions-holder profile-settings__actions d-none">
                            <div class="d-flex">
                                <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button profile-settings__action-btn" data-toggle="tooltip" data-placement="top" title="{{ __('Upload cover image') }}">
                                    @include('elements.icon', ['icon' => 'image', 'variant' => 'medium'])
                                </span>
                                <span class="h-pill h-pill-accent pointer-cursor profile-settings__action-btn" onclick="ProfileSettings.removeUserAsset('cover')" data-toggle="tooltip" data-placement="top" title="{{ __('Remove cover image') }}">
                                    @include('elements.icon', ['icon' => 'close', 'variant' => 'medium'])
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-settings__avatar-wrap">
                <div class="card avatar-holder profile-settings__avatar">
                    <img class="card-img-top profile-settings__avatar-img" src="{{ Auth::user()->avatar }}" alt="">
                    <div class="card-img-overlay profile-settings__avatar-overlay d-flex justify-content-center align-items-center">
                        <div class="actions-holder profile-settings__actions d-none">
                            <div class="d-flex">
                                <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button profile-settings__action-btn" data-toggle="tooltip" data-placement="top" title="{{ __('Upload avatar') }}">
                                    @include('elements.icon', ['icon' => 'image', 'variant' => 'medium'])
                                </span>
                                <span class="h-pill h-pill-accent pointer-cursor profile-settings__action-btn" onclick="ProfileSettings.removeUserAsset('avatar')" data-toggle="tooltip" data-placement="top" title="{{ __('Remove avatar') }}">
                                    @include('elements.icon', ['icon' => 'close', 'variant' => 'medium'])
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success profile-settings__alert text-white font-weight-bold" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="profile-settings__fields">
            <div class="form-group profile-settings__field">
                <label for="username">{{ __('Username') }}</label>
                <input class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username" value="{{ Auth::user()->username }}">
                @if($errors->has('username'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group profile-settings__field">
                <label for="name">{{ __('Full name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" value="{{ Auth::user()->name }}">
                @if($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group profile-settings__field">
                <div class="profile-settings__label-row">
                    <label for="bio">{{ __('Bio') }}</label>
                    @if(getSetting('ai.open_ai_enabled'))
                        <a href="javascript:void(0)" class="profile-settings__suggest-link" onclick="{{ 'AiSuggestions.suggestDescriptionDialog();' }}" data-toggle="tooltip" data-placement="left" title="{{ __('Use AI to generate your description.') }}">{{ trans_choice('Suggestion', 2) }}</a>
                    @endif
                </div>
                <textarea class="form-control {{ $errors->has('bio') ? 'is-invalid' : '' }}" id="bio" name="bio" rows="3" spellcheck="false">{{ Auth::user()->bio }}</textarea>
                @if($errors->has('bio'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('bio') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group profile-settings__field">
                <label for="birthdate">{{ __('Birthdate') }}</label>
                <div class="profile-settings__date-wrap">
                    <input type="date" class="form-control {{ $errors->has('birthdate') ? 'is-invalid' : '' }}" id="birthdate" name="birthdate" value="{{ Auth::user()->birthdate }}" max="{{ $minBirthDate }}">
                </div>
                @if($errors->has('birthdate'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('birthdate') }}</strong>
                    </span>
                @endif
            </div>

            <div class="profile-settings__row">
                <div class="profile-settings__row-col {{ getSetting('profiles.allow_gender_pronouns') ? 'profile-settings__row-col--half' : 'profile-settings__row-col--full' }}">
                    <div class="form-group profile-settings__field">
                        <label for="gender">{{ __('Gender') }}</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="" disabled {{ Auth::user()->gender_id ? '' : 'selected' }}>{{ __('Select Gender') }}</option>
                            @foreach($genders as $gender)
                                <option value="{{ $gender->id }}" {{ Auth::user()->gender_id == $gender->id ? 'selected' : '' }}>{{ __($gender->gender_name) }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('gender'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('gender') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                @if(getSetting('profiles.allow_gender_pronouns'))
                    <div class="profile-settings__row-col profile-settings__row-col--half">
                        <div class="form-group profile-settings__field">
                            <label for="pronoun">{{ __('Gender pronoun') }}</label>
                            <input class="form-control {{ $errors->has('pronoun') ? 'is-invalid' : '' }}" id="pronoun" name="pronoun" value="{{ Auth::user()->gender_pronoun }}">
                            @if($errors->has('pronoun'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('pronoun') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="profile-settings__row">
                <div class="profile-settings__row-col profile-settings__row-col--half">
                    <div class="form-group profile-settings__field">
                        <label for="country">{{ __('Country') }}</label>
                        <select class="form-control" id="country" name="country">
                            <option value="" disabled {{ Auth::user()->country_id ? '' : 'selected' }}>{{ __('Select country') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ Auth::user()->country_id == $country->id ? 'selected' : '' }}>{{ __($country->name) }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('country'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('country') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="profile-settings__row-col profile-settings__row-col--half">
                    <div class="form-group profile-settings__field">
                        <label for="location">{{ __('Location') }}</label>
                        <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="location" name="location" value="{{ Auth::user()->location }}">
                        @if($errors->has('location'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('location') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group profile-settings__field">
                <label for="website">{{ __('Website URL') }}</label>
                <input type="url" class="form-control {{ $errors->has('website') ? 'is-invalid' : '' }}" id="website" name="website" value="{{ Auth::user()->website }}">
                @if($errors->has('website'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('website') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <button class="btn btn-primary profile-settings__submit" type="submit">{{ __('Save') }}</button>
    </form>
</div>
