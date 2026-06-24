@extends('layouts.user-no-nav')

@section('page_title', __('Messenger'))

@section('styles')
    {!!
        Minify::stylesheet([
            '/libs/@selectize/selectize/dist/css/selectize.css',
            '/libs/@selectize/selectize/dist/css/selectize.bootstrap4.css',
            '/libs/dropzone/dist/dropzone.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/messenger.css',
            '/css/pages/checkout.css'
         ])->withFullUrl()
    !!}
@stop

@section('scripts')
    {!!
        Minify::javascript([
            '/js/messenger/messenger.js',
            '/js/messenger/elements.js',
            '/libs/@selectize/selectize/dist/js/standalone/selectize.min.js',
            '/libs/dropzone/dist/dropzone.js',
            '/js/FileUpload.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
            '/js/pages/lists.js',
            '/js/pages/checkout.js',
            '/libs/pusher-js-auth/lib/pusher-auth.js'
         ])->withFullUrl()
    !!}
@stop

@section('content')
    @include('elements.uploaded-file-preview-template')
    @include('elements.photoswipe-container')
    @include('elements.report-user-or-post',['reportStatuses' => ListsHelper::getReportTypes()])
    @include('elements.feed.post-delete-dialog')
    @include('elements.feed.post-list-management')
    @include('elements.messenger.message-price-dialog')
    @include('elements.checkout.checkout-box')
    @include('elements.attachments-uploading-dialog')
    @include('elements.messenger.locked-message-no-attachments-dialog')
    <div class="row no-gutters">
        <div class="col-12">
            <div class="container messenger">
                <div class="row no-gutters h-100">
                    <div class="col-12 col-md-4 col-lg-3 conversations-wrapper">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-truncate {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{__('Messages')}}</h5>
                            <span data-toggle="tooltip" title="" class="pointer-cursor"
                                  @if(!count($availableContacts))
                                    data-original-title="{{trans_choice('Before sending a new message, please subscribe to a creator a follow a free profile.',['user' => 0])}}"
                                  @else
                                    data-original-title="{{trans_choice('Send a new message',['user' => 0])}}"
                                  @endif
                            >
                                <a title="" class="pointer-cursor new-conversation-toggle" data-original-title="{{trans_choice('Send a new message',['user' => 0])}}">
                                    <div class="h5 mb-0">@include('elements.icon',['icon'=>'create-outline','variant'=>'medium'])</div>
                                </a>
                            </span>
                        </div>
                        <div class="conversations-list">
                            @if($lastContactID == false)
                                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                                    <span class="text-muted text-center px-3">{{__('Click the text bubble to send a new message.')}}</span>
                                </div>
                            @else
                                @include('elements.preloading.messenger-contact-box', ['limit'=>3])
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-8 col-lg-9 conversation-wrapper">
                        @include('elements.message-alert')
                        @include('elements.messenger.messenger-conversation-header')
                        @include('elements.messenger.messenger-new-conversation-header')
                        @include('elements.preloading.messenger-conversation-header-box')
                        @include('elements.preloading.messenger-conversation-box')
                        <div class="conversation-content flex-fill">
                        </div>
                        <div class="dropzone-previews dropzone w-100"></div>
                        <div class="conversation-writeup {{!$lastContactID ? 'hidden' : ''}}">
                            <div class="messenger-buttons-wrapper">
                                <div class="dropup">
                                    <button class="btn btn-outline-primary btn-rounded-icon messenger-button dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{__('More')}}">
                                        <div class="d-flex justify-content-center align-items-center">
                                            @include('elements.icon',['icon'=>'ellipsis-horizontal-outline','variant'=>''])
                                        </div>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="messenger.openPrivateRequestModal(); return false;">
                                            @include('elements.icon',['icon'=>'gift-outline','variant'=>''])
                                            <span class="ml-2">{{__('Private request')}}</span>
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="$('.file-upload-button').trigger('click'); return false;">
                                            @include('elements.icon',['icon'=>'document','variant'=>''])
                                            <span class="ml-2">{{__('Attach file')}}</span>
                                        </a>
                                        @if((GenericHelper::creatorCanEarnMoney(Auth::user()) && !(!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks'))) /*|| Auth::user()->role_id === 1*/)
                                            <a class="dropdown-item" href="#" onclick="messenger.showSetPriceDialog(); return false;">
                                                @include('elements.icon',['icon'=>'lock-open','variant'=>''])
                                                <span class="ml-2">{{__('Message price')}}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary btn-rounded-icon messenger-button attach-file file-upload-button d-none" aria-hidden="true" tabindex="-1" type="button">
                                    <div class="d-flex justify-content-center align-items-center">
                                        @include('elements.icon',['icon'=>'document','variant'=>''])
                                    </div>
                                </button>
                            </div>
                            <form class="message-form">
                                <div class="input-group messageBoxInput-wrapper">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="receiverID" id="receiverID" value="">
                                    <textarea name="message" class="form-control messageBoxInput dropzone" placeholder="{{__('Write a message..')}}" onkeyup="messenger.textAreaAdjust(this)" rows="1"></textarea>
                                </div>
                            </form>
                            <div class="messenger-buttons-wrapper">
                                @if((GenericHelper::creatorCanEarnMoney(Auth::user()) && !(!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks'))) /*|| Auth::user()->role_id === 1*/)
                                    <span class="d-none">
                                        <button class="btn btn-outline-primary btn-rounded-icon messenger-button to-tooltip" data-placement="top" title="{{__('Message price')}}" onClick="messenger.showSetPriceDialog()">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <span class="message-price-lock">@include('elements.icon',['icon'=>'lock-open','variant'=>''])</span>
                                                <span class="message-price-close d-none">@include('elements.icon',['icon'=>'lock-closed','variant'=>''])</span>
                                            </div>
                                        </button>
                                    </span>
                                @endif
                                <button class="btn btn-outline-primary btn-rounded-icon messenger-button send-message to-tooltip" onClick="messenger.sendMessage()" data-placement="top" title="{{__('Send message')}}">
                                    <div class="d-flex justify-content-center align-items-center">
                                        @include('elements.icon',['icon'=>'paper-plane','variant'=>''])
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('elements.standard-dialog',[
    'dialogName' => 'message-delete-dialog',
    'title' => __('Delete message'),
    'content' => __('Are you sure you want to delete this message?'),
    'actionLabel' => __('Delete'),
    'actionFunction' => 'messenger.deleteMessage();',
])
@stop
