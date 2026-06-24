/**
 *
 * Main App Component
 *
 */
"use strict";
/* global app, user, pusher, Pusher, PostsPaginator, notifications, filterXSS, soketi, socketsDriver, messenger, FileUpload, videoPreview */

// Wait for jQuery to be loaded
window.addEventListener('load', function() {
    if (typeof jQuery !== 'undefined') {
        // Init
        $(function () {
            log('🚀 © JustFans Loaded © 🚀');

            // Instantiating default actions if installed
            if(typeof app !== 'undefined'){

                if(app.showCookiesBox !== null){
                    var br = bootstrapDetectBreakpoint();
                    if(br === null){
                        br = {name : 'lg'};
                    }
                    let cookiesConsetOptions = {
                        "theme": "classic",
                        "position": (br.name !== 'xs' ? "bottom-right" : "bottom"),
                        dismissOnScroll: 100,
                        dismissOnWindowClick: true,
                        "palette": {
                            "popup": {
                                "background": "#efefef",
                                "text": "#404040"
                            },
                            "button": {
                                "background": "#007BFF",
                                "text": "#ffffff"
                            }
                        },
                        content: {
                            message: trans( `🍪 ${trans('This website uses cookies to improve your experience.')}`),
                            dismiss: trans(`Got it!`),
                            link: trans('Learn more'),
                            href: "http://cookies.insites.com/about-cookies"
                        },
                    };
                    if(br.name === 'xs'){
                        cookiesConsetOptions.dismissOnScroll = 100;
                    }
                    window.cookieconsent.initialise(cookiesConsetOptions);
                }

                // Check if age verification dialog should be enabled
                if (app.enable_age_verification_dialog &&
                    !isSlugInUrl(app.tosPageSlug) &&
                    !isSlugInUrl(app.privacyPageSlug) &&
                    window.location.href.indexOf('invoices') === -1) {

                    const classes = 'body .flex-fill, footer, .global-announcement-banner, .navbar';

                    if (!getCookie('site_entry_approval')) {
                        // Show modal and add blur class to multiple elements
                        $('#site-entry-approval-dialog').modal('show');
                        const elementsToBlur = $(classes);
                        elementsToBlur.addClass('blurred');
                    }

                    // Remove blur class when modal is hidden
                    $('#site-entry-approval-dialog').on('hidden.bs.modal', function () {
                        const elementsToBlur = $(classes);
                        elementsToBlur.removeClass('blurred');
                    });
                }

                // Auto-including the CSRF token in all AJAX Requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });

                // Globally handling AJAX requests, especially for handling expired tokens and sesisions
                // TODO: Decide if this should be left enabled on prod mode or if it would help clients more
                $(document).ajaxError(function (event, jqXHR) {
                    if (jqXHR.status === 0) {
                        log('Not connect.n Verify Network.', 'error');
                    } else if (jqXHR.status === 404) {
                        log('Requested page not found. [404]', 'error');
                    } else if (jqXHR.status === 500) {
                        log('Internal Server Error [500].', 'error');
                    } else if (jqXHR.status === 401) {
                        log('Session expired. Redirecting you to refresh the session.', 'error');
                        redirect(app.baseUrl);
                    } else if (jqXHR.status === 408) {
                        reload();
                    } else {
                        log('Uncaught Error.n' + jqXHR.responseText, 'error');
                    }
                });

                // Displaying error messages for expired sessions
                if (app.sessionStatus === 'expired') {
                    launchToast('info', 'Session expired ', 'Page refreshed', 'now');
                }

                // Dark mode switcher event
                $('.dark-mode-switcher').on('click', function () {
                    let currentTheme = getCookie('app_theme');
                    if (currentTheme === 'dark') {
                        setCookie('app_theme', 'light', 365);
                    } else {
                        setCookie('app_theme', 'dark', 365);
                    }
                    reload();
                });

                // RTL mode switcher event
                $('.rtl-mode-switcher').on('click', function () {
                    let currentTheme = getCookie('app_rtl');
                    if (currentTheme === 'rtl') {
                        setCookie('app_rtl', 'ltr', 365);
                    } else {
                        setCookie('app_rtl', 'rtl', 365);
                    }
                    reload();
                });

                // Initialize tooltips
                initTooltips();

                if(window.location.href.indexOf('register') >= 0){
                    // Forcing TOS checkbox for social auth
                    $('.social-login-links a').on('click', function (event) {
                        if($('#tosAgree').is(':checked') === false){
                            event.preventDefault();
                            $('#tosAgree').addClass('is-invalid');
                        }
                    });
                }

                // Initialize user connection to pusher
                try {
                    const location = window.location.href;

                    // Enable pusher logging - don't include this in production
                    Pusher.logToConsole = pusher.logging;
                    let params = {
                        cluster: pusher.cluster
                    };
                    if(socketsDriver === 'soketi'){
                        params = {
                            wsHost: soketi.host,
                            wsPort: soketi.port,
                            forceTLS: soketi.useTSL ? true : false,
                        };
                    }
                    var pusherClient = new Pusher(socketsDriver === 'soketi' ? soketi.key : pusher.key, params);
                    var channel = pusherClient.subscribe(user.username);

                    // Binding the new notifications
                    channel.bind('new-notification', function (data) {
                        let toastTitle = trans('Notification');
                        if(data.type === 'new-message'){
                            toastTitle = 'New message';
                            incrementNotificationsCount('.menu-notification-badge.chat-menu-count');
                        }
                        incrementNotificationsCount('.menu-notification-badge.notifications-menu-count');

                        if (window.location.href !== null && window.location.href.indexOf('/my/notifications') >= 0) {
                            notifications.updateUserNotificationsList(this.getNotificationsActiveFilter());
                        }
                        if(location.indexOf('my/messenger') >= 0 && data.type === 'new-message') {
                            return true;
                        }
                        launchToast('success', trans(toastTitle), filterXSS(data.message));
                    });

                    // Binding global messenger events
                    channel.bind('messenger-actions', function (data) {
                        if(data.type === 'new-messenger-conversation' && window.location.href.indexOf('my/messenger') >= 0){
                            messenger.fetchContacts();
                            messenger.fetchConversation(data.notification.fromUserID);
                            messenger.hideEmptyChatElements();
                            messenger.reloadConversationHeader();
                        }
                    });

                    // Binding global video-processing events
                    if(location.indexOf('posts/create') >= 0 || location.indexOf('posts/edit')){
                        channel.bind('video-processing', function (data) {
                            // Updating our inner attachments state
                            FileUpload.attachaments = FileUpload.attachaments.map((element) => {
                                if (element.attachmentID === data.id) {
                                    const updatedElement = {
                                        "attachmentID": data.id,
                                        "path": data.path,
                                        "thumbnail": data.thumbnail,
                                        "type": 'video'
                                    };
                                    return updatedElement;
                                }
                                return element;
                            });

                            // Altering the dropzone uploaded files state and preview
                            // Todo: This will throw an error when webhook event is received on a different page
                            FileUpload.myDropzone.files.map((file) => {
                                if(file.upload.attachmentID === data.id){
                                    if(data.success){
                                        let filePreview = $(file.previewElement);
                                        filePreview.find('.video-preview-item').remove();
                                        filePreview.prepend(videoPreview());
                                        var videoPreviewEl = filePreview.find('video').get(0);
                                        FileUpload.setPreviewSource(videoPreviewEl, file, data);
                                        FileUpload.isTranscodingVideo = false;
                                    }
                                    else{
                                        FileUpload.myDropzone.removeFile(file); // Note: This also clears up FileUpload.attachaments
                                        launchToast('danger',trans('Error'), trans('A video encoding error has occurred. Please contact the administrator if this error persists.'));
                                    }
                                }
                            });

                        });
                    }


                } catch (e) {
                    // eslint-disable-next-line no-console
                    console.warn(trans('Pusher initialization failed'));
                    // eslint-disable-next-line no-console
                    console.warn(e);
                }

            }

        });
    } else {
        console.error('jQuery is not loaded');
    }
});

$(window).scroll(function () {
    if(typeof skipDefaultScrollInits === 'undefined'){
        if($('.side-menu').length){
            initStickyComponent('.side-menu','sticky');
        }
    }
});

/**
 * Log function sugar syntax
 * @param v
 */
function log(v,type = 'log') {
    if(typeof app !== 'undefined' && app.debug){
        switch (type) {
        case 'info':
            // eslint-disable-next-line no-console
            console.info(v);
            break;
        case 'log':
            // eslint-disable-next-line no-console
            console.log(v);
            break;
        case 'warn':
            // eslint-disable-next-line no-console
            console.warn(v);
            break;
        case 'error':
            // eslint-disable-next-line no-console
            console.error(v);
            break;
        }
    }
    return true;
}

/**
 * Instantiates tooltips
 */
function initTooltips(){
    try {
        if (typeof $ !== 'undefined' && typeof $.fn !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
            $('.to-tooltip').tooltip();
        } else {
            console.log('Bootstrap tooltip not available, using fallback');
            
            // Simple tooltip fallback
            document.querySelectorAll('[data-toggle="tooltip"], .to-tooltip').forEach(function(element) {
                const title = element.getAttribute('title') || element.getAttribute('data-original-title');
                if (title) {
                    // Store title for later use
                    element.setAttribute('data-tooltip-text', title);
                    element.removeAttribute('title');
                    
                    // Add event listeners
                    element.addEventListener('mouseenter', showTooltipFallback);
                    element.addEventListener('mouseleave', hideTooltipFallback);
                }
            });
        }
    } catch (e) {
        console.error('Error initializing tooltips:', e);
    }
}

// Tooltip fallback functions
function showTooltipFallback(e) {
    const tooltipText = this.getAttribute('data-tooltip-text');
    if (!tooltipText) return;
    
    // Create tooltip
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip-fallback';
    tooltip.textContent = tooltipText;
    
    // Position tooltip
    const rect = this.getBoundingClientRect();
    const tooltipStyles = {
        position: 'fixed',
        top: (rect.top - 35) + 'px',
        left: (rect.left + rect.width/2) + 'px',
        transform: 'translateX(-50%)',
        backgroundColor: 'rgba(0,0,0,0.8)',
        color: 'white',
        padding: '5px 10px',
        borderRadius: '4px',
        fontSize: '12px',
        zIndex: '9999',
        pointerEvents: 'none'
    };
    
    Object.assign(tooltip.style, tooltipStyles);
    document.body.appendChild(tooltip);
    
    // Store reference to remove later
    this._tooltipElement = tooltip;
}

function hideTooltipFallback() {
    if (this._tooltipElement) {
        document.body.removeChild(this._tooltipElement);
        this._tooltipElement = null;
    }
}

/**
 * Redirect function
 * @param url
 */
function redirect(url) {
    window.location.href = url;
}

/**
 * Submits the search form
 */
// eslint-disable-next-line no-unused-vars
function submitSearch() {
    $('.search-box-wrapper').submit();
}

/**
 * Page reload function
 */
function reload() {
    return window.location.reload();
}

/**
 * Copy to clipboard function
 * @param textToCopy
 */
function copyToClipboard(textToCopy, container = 'body') {
    let $temp = $("<textarea>");
    $(container).append($temp);
    $temp.val(textToCopy).select();
    document.execCommand("copy");
    $temp.remove();
}

/**
 * Attaches scroll handlers & sticky behaviour to desired components
 * @param component
 * @param stickyClass
 */
function initStickyComponent(component,stickyClass) {
    let sticky = false;
    let top = $(window).scrollTop();
    if ($(".main-wrapper").offset().top < top) {
        $(component).addClass(stickyClass);
        // eslint-disable-next-line no-unused-vars
        sticky = true;
    } else {
        $(".side-menu, .suggestions-box").removeClass(stickyClass);
    }
}

/**
 * Go to login via UI redirect
 */
// eslint-disable-next-line no-unused-vars
function goToLogin() {
    redirect(app.baseUrl + '/login');
}

/**
 * Accepts adult content confirm dialog
 */
// eslint-disable-next-line no-unused-vars
function acceptSiteEntry() {
    setCookie('site_entry_approval',true,90);
    $('#site-entry-approval-dialog').modal('hide');
}

/**
 * Set cookie
 * @param key
 * @param value
 * @param expiry
 */
function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
}

/**
 * Get cookie value
 * @param key
 * @returns {any}
 */
function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

/**
 * Delete cookie
 * @param key
 */
// eslint-disable-next-line no-unused-vars
function eraseCookie(key) {
    var keyValue = getCookie(key);
    setCookie(key, keyValue, '-1');
}

/**
 * Reload themes on the fly
 */
// eslint-disable-next-line no-unused-vars
function reloadTheme() {
    let appTheme = 'css/bootstrap/bootstrap';
    let currentTheme = getCookie('app_theme');
    let currentRTLSetting = getCookie('app_rtl');
    if (currentRTLSetting === 'rtl') {
        appTheme += '.rtl';
    }

    if (currentTheme === 'dark') {
        appTheme += '.dark';
    }
    appTheme += ".css";
    $('#app-theme').attr('href', appTheme);
}

/**
 * Launches custom, stackable and dismisable toasts
 * @param type
 * @param title
 * @param message
 * @param subtitle
 */
function launchToast(type, title, message, subtitle = '') {
    try {
        if (typeof $ !== 'undefined' && typeof $.toast !== 'undefined') {
            $.toast({
                type: '',
                title: title,
                subtitle: subtitle,
                content: message,
                dismissible: true,
                indicator: {
                    type: type
                },
                delay: 5000,
            });
        } else {
            // Fallback toast implementation
            console.log('[Toast Fallback]', type, title, message);
            
            // Create toast element
            var toast = document.createElement('div');
            toast.className = 'fallback-toast toast-' + type;
            toast.innerHTML = '<div class="toast-header">' + title + '</div><div class="toast-body">' + message + '</div>';
            
            // Style the toast
            Object.assign(toast.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                zIndex: '9999',
                backgroundColor: type === 'success' ? '#28a745' : (type === 'danger' ? '#dc3545' : '#17a2b8'),
                color: 'white',
                padding: '10px 15px',
                borderRadius: '5px',
                boxShadow: '0 0.5rem 1rem rgba(0, 0, 0, 0.15)',
                opacity: '0',
                transition: 'opacity 0.3s ease-in-out',
                maxWidth: '300px',
                margin: '10px'
            });
            
            // Add to document
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(function() {
                toast.style.opacity = '1';
            }, 10);
            
            // Remove after delay
            setTimeout(function() {
                toast.style.opacity = '0';
                setTimeout(function() {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }
    } catch (error) {
        console.error('Error showing toast:', error);
        // Ultimate fallback - alert
        alert(title + ': ' + message);
    }
}

// Create fallback for $.toast if jQuery is available but plugin isn't
if (typeof $ !== 'undefined' && typeof $.toast === 'undefined') {
    $.toast = function(opts) {
        var type = opts.indicator ? opts.indicator.type : 'info';
        var title = opts.title || '';
        var message = opts.content || '';
        launchToast(type, title, message, opts.subtitle);
    };
}

/**
 * Opens up device share API or fallbacks to URL copy
 * @param url
 */
// eslint-disable-next-line no-unused-vars
function shareOrCopyLink(url = false) {
    if (url === false) {
        url = window.location.href;
    }
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: url
        })
            // eslint-disable-next-line no-console
            .then(() => console.log('Successful share'))
            // eslint-disable-next-line no-console
            .catch(error => console.log('Error sharing:', error));
    } else {
        copyToClipboard(url);
        launchToast('success', trans('Success'), trans('Link copied to clipboard')+'.', 'now');
    }
}

/**
 * Auto Adjusts textareas on resize
 * @param el
 */
// eslint-disable-next-line no-unused-vars
function textAreaAdjust(el) {
    el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight) + "px" : "45px";
}

/**
 * Filters up user received notifications ( via sockets )
 * @returns {string}
 */
// eslint-disable-next-line no-unused-vars
function getNotificationsActiveFilter() {
    let activeType = '';
    // get active filter if exists
    if (window.location.href.indexOf('/likes') >= 0) {
        activeType = '/likes';
    } else if (window.location.href.indexOf('/messages') >= 0) {
        activeType = '/messages';
    } else if (window.location.href.indexOf('/subscriptions') >= 0) {
        activeType = '/subscriptions';
    } else if (window.location.href.indexOf('/tips') >= 0) {
        activeType = '/tips';
    } else if (window.location.href.indexOf('/promos') >= 0) {
        activeType = '/promos';
    }

    return activeType;
}

/**
 * Method used for translating locale strings
 * @param key
 * @param replace
 * @returns {T|*}
 */
function trans(key, replace = {})
{
    if (!window.translations) {
        window.translations = {};
    }
    let translation = window.translations[key];
    if(translation === null || typeof translation === 'undefined'){ // If no translation available, return the key
        return key;
    }
    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }
    if(typeof translation === 'undefined'){
        return key;
    }
    return translation;
}

/**
 * Method used for translating locale strings
 * Supports multiple choices translations
 * @param key
 * @param replace
 * @returns {T|*}
 */
// eslint-disable-next-line no-unused-vars
function trans_choice(key, count = 1, replace = {})
{
    let keyValue = window.translations[key];
    if(typeof keyValue === 'undefined'){
        return key;
    }
    const translations = keyValue.split('|');
    let translation = count > 1 || count === 0 ? translations[1] : translations[0];
    translation = translation.replace('[2,*]','');

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }
    return translation;
}

/**
 * Updates button state, adding loading icon to it and disabling it
 * @param state
 * @param buttonElement
 */
// eslint-disable-next-line no-unused-vars
function updateButtonState(state, buttonElement, buttonContent = false, loadingColor = 'primary'){
    if(state === 'loaded'){
        if(buttonContent){
            buttonElement.html(buttonContent);
        }
        else{
            buttonElement.html('<div class="d-flex justify-content-center align-items-center"><ion-icon name="paper-plane"></ion-icon></div>');
        }
        buttonElement.removeClass('disabled');
    }
    else{
        buttonElement.html( `<div class="d-flex justify-content-center align-items-center">
            <div class="spinner-border text-${loadingColor} spinner-border-sm" role="status">
            <span class="sr-only">${trans('Loading...')}</span>
            </div>
            ${(buttonContent !== false ? '<div class="ml-2">'+buttonContent+'</div>' : '')}
            </div>`);
        buttonElement.addClass('disabled');
    }
}

/**
 * Re-sends the user email verification
 * @param callback
 */
// eslint-disable-next-line no-unused-vars
function sendEmailConfirmation(callback = function(){}){
    $('.unverified-email-box').attr('onClick','');
    $.ajax({
        url:app.baseUrl +'/resendVerification',
        type:'POST',
        success : function(){
            $('.unverified-email-box').fadeOut();
            launchToast('success', trans('Success'), trans('Confirmation email sent. Please check your inbox and spam folder.'), 'now');
            callback();
        },
        error: function () {

        }
    });
}

/**
 * Preps a data beacon data sample, to be saved before page unload
 * @returns {FormData}
 */
// eslint-disable-next-line no-unused-vars
function prepBeaconDataSample(){
    var fd = new FormData();
    fd.append('prevPage', PostsPaginator.currentPage);
    return fd;
}

/**
 * Returns current bootstrap breakpoint to the JS side
 * @returns {{name: (string|string), index: number}|null}
 */
// eslint-disable-next-line no-unused-vars
function bootstrapDetectBreakpoint() {
    // cache some values on first call
    let breakpointNames = ["xl", "lg", "md", "sm", "xs"];
    let breakpointValues = [];
    for (const breakpointName of breakpointNames) {
        breakpointValues[breakpointName] = window.getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-' + breakpointName);
    }
    let i = breakpointNames.length;
    for (const breakpointName of breakpointNames) {
        i--;
        if (window.matchMedia("(min-width: " + breakpointValues[breakpointName] + ")").matches) {
            return {name: breakpointName, index: i};
        }
    }
    return null;
}

/**
 * Increments the notifications badge by 1 or adds it if it doesnt exist
 */
function incrementNotificationsCount(selector, value = 1) {
    if(parseInt($(selector).html()) + (value) > 0){
        $(selector).removeClass('d-none');
        $(selector).html(parseInt($(selector).html()) + (value));
    }
    else{
        $(selector).html('0');
        $(selector).addClass('d-none');
    }
}

/**
 * Checks if creator can post a PPV post within the limits
 */
// eslint-disable-next-line no-unused-vars
function passesMinMaxPPPostLimits(price) {
    let hasError = false;
    if(parseInt(price) < parseInt(app.min_ppv_post_price)){
        hasError = true;
    }
    if(parseInt(price) > parseInt(app.max_ppv_post_price)){
        hasError = true;
    }
    if(price.length <= 0){
        hasError = true;
    }
    return !hasError;
}

/**
 * Checks if creator can post a PPV message within the limits
 * @param price
 * @returns {boolean}
 */
// eslint-disable-next-line no-unused-vars
function passesMinMaxPPVMessageLimits(price) {
    let hasError = false;
    if(parseInt(price) < parseInt(app.min_ppv_message_price)){
        hasError = true;
    }
    if(parseInt(price) > parseInt(app.max_ppv_message_price)){
        hasError = true;
    }
    if(price.length <= 0){
        hasError = true;
    }
    return !hasError;
}


// eslint-disable-next-line no-unused-vars
function showDialog(dialogID){
    $('#' + dialogID).modal('show');
}

// eslint-disable-next-line no-unused-vars
function hideDialog(dialogID){
    $('#' + dialogID).modal('hide');
}

// eslint-disable-next-line no-unused-vars
function openLanguageSelectorDialog() {
    $('#language-selector-dialog').modal('show');
}

// eslint-disable-next-line no-unused-vars
function setUserLanguage() {
    let languageLink = app.baseUrl + '/language/' + $('#language_code').val();
    window.location.href = languageLink;
}

// eslint-disable-next-line no-unused-vars
function getWebsiteFormattedAmount(amount){
    let currencyPosition = app.currencyPosition;
    let currency = app.currencySymbol;

    return currencyPosition === 'left' ? currency + amount : amount + currency;
}

// eslint-disable-next-line no-unused-vars
function getTaxDescription(taxName, taxPercentage, taxType){
    if(taxType !== 'fixed') {
        let type = taxType === 'inclusive' ? ' incl.' : '';
        return taxName + " (" + taxPercentage + "%" + type + ")";
    }
    return taxName;
}

/**
 * Detects if CSS's line-clamp property is actively cutting off content
 * @param selector
 * @returns {boolean}
 */
// eslint-disable-next-line no-unused-vars
function multiLineOverflows(selector) {
    const el = document.querySelector(selector);
    if (el) {
        return el.scrollHeight > el.clientHeight;
    }
    return false;
}

// eslint-disable-next-line no-unused-vars
function dimissGlobalAnnouncement(id) {
    $.ajax({
        url:app.baseUrl +'/markBannerAsSeen',
        type:'POST',
        data : {id: id},
        success : function(){
            // Placeholders
        },
        error: function () {
            // Placeholders
        }
    });
}
// eslint-disable-next-line no-unused-vars
function bindNoLongPressEvents() {
    var pressTimer;
    // Unbind previous event handlers to prevent multiple bindings
    $('.no-long-press').off('touchstart touchend touchmove contextmenu');
    // Bind the touchstart event
    $('.no-long-press').on('touchstart', function(e) {
        pressTimer = window.setTimeout(function() {
            e.preventDefault();
            // console.log('long press');
        }, 500);
    });
    // Bind the touchend and touchmove events
    $('.no-long-press').on('touchend touchmove', function() {
        clearTimeout(pressTimer);
    });
    // Prevent the context menu from appearing
    $('.no-long-press').on('contextmenu', function(e) {
        e.preventDefault();
    });
}

// Function to check if URL contains specific slug
function isSlugInUrl(slug) {
    return slug !== null && window.location.href.indexOf(slug) >= 0;
}
