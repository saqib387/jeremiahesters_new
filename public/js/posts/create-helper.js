/**
 * Post create (helper) component
 */
"use strict";
/* global app, Post, user, FileUpload, updateButtonState, launchToast, trans, redirect, trans_choice, mediaSettings, getWebsiteFormattedAmount, passesMinMaxPPPostLimits */

$(function () {
    $("#post-price").keypress(function(e) {
        if(e.which === 13) {
            PostCreate.savePostPrice();
        }
    });
});

var PostCreate = {
    // Paid post price
    postPrice : 0,
    isSavingRedirect: false,
    postNotifications: false,
    postReleaseDate: null,
    postExpireDate: null,

    /**
     * Toggles post notification state
     */
    togglePostNotifications: function(){
        let buttonIcon = '';
        if(PostCreate.postNotifications === true){
            PostCreate.postNotifications = false;
            buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-off-outline"></ion-icon></div>`;
        }
        else{
            buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-outline"></ion-icon></div>`;
            PostCreate.postNotifications = true;
        }
        $('.post-notification-icon').html(buttonIcon);
    },

    /**
     * Shows up the post price setter dialog
     */
    showSetPricePostDialog: function(){
        try {
            if (typeof $.fn !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                $('#post-set-price-dialog').modal('show');
            } else {
                console.log('Bootstrap modal not available, using fallback.');
                this.showModalFallback('#post-set-price-dialog');
            }
        } catch (e) {
            console.error('Error showing price dialog:', e);
            // Fallback method
            this.showModalFallback('#post-set-price-dialog');
        }
    },
    
    /**
     * Fallback method to show modal without Bootstrap's modal plugin
     */
    showModalFallback: function(selector) {
        var modal = document.querySelector(selector);
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop if it doesn't exist
            if (!document.querySelector('.modal-backdrop')) {
                var backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
            
            // Add close handlers
            var closeButtons = modal.querySelectorAll('[data-dismiss="modal"], .close');
            closeButtons.forEach(function(btn) {
                btn.onclick = function() {
                    PostCreate.hideModalFallback(selector);
                };
            });
        }
    },
    
    /**
     * Fallback method to hide modal without Bootstrap's modal plugin
     */
    hideModalFallback: function(selector) {
        var modal = document.querySelector(selector);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            
            // Remove backdrop
            var backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.parentNode.removeChild(backdrop);
            }
        }
    },

    /**
     * Saves the post price into the state
     */
    savePostPrice: function(){
        PostCreate.postPrice = $('#post-price').val();
        let hasError = false;
        if(!passesMinMaxPPPostLimits(PostCreate.postPrice)){
            hasError = 'min';
        }
        if(PostCreate.postExpireDate !== null){
            hasError = 'ppv';
        }
        if(hasError){
            $('.post-price-error').addClass('d-none');
            $('#post-set-price-dialog .'+hasError+'-error').removeClass('d-none');
            $('#post-price').addClass('is-invalid');
            return false;
        }
        $('.post-price-label').html('('+getWebsiteFormattedAmount(PostCreate.postPrice)+')');
        
        try {
            if (typeof $.fn !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                $('#post-set-price-dialog').modal('hide');
            } else {
                this.hideModalFallback('#post-set-price-dialog');
            }
        } catch (e) {
            console.error('Error hiding price dialog:', e);
            this.hideModalFallback('#post-set-price-dialog');
        }
        
        $('#post-price').removeClass('is-invalid');
    },
    /**
     * Clears up post price
     */
    clearPostPrice: function(){
        PostCreate.postPrice = 0;
        $('#post-price').val(0);
        $('.post-price-label').html('');
        
        try {
            if (typeof $.fn !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                $('#post-set-price-dialog').modal('hide');
            } else {
                this.hideModalFallback('#post-set-price-dialog');
            }
        } catch (e) {
            console.error('Error hiding price dialog:', e);
            this.hideModalFallback('#post-set-price-dialog');
        }
        
        $('#post-price').removeClass('is-invalid');
    },

    /**
     * Initiates the post draft data, if available
     * @param data
     * @param type
     */
    initPostDraft: function(data, type = 'draft'){
        Post.initialDraftData = Post.draftData;
        if(data){
            Post.draftData = data;
            if(type === 'draft'){
                FileUpload.attachaments = data.attachments;
            }
            else{
                data.attachments.map(function (item) {
                    FileUpload.attachaments.push({attachmentID: item.id, path: item.path, type:item.attachmentType, thumbnail:item.thumbnail});
                });
            }
            $('#dropzone-uploader').val(Post.draftData.text);
        }
    },

    /**
     * Clears up post draft data
     */
    clearDraft: function(){
        // Clearing attachments from the backend
        Post.draftData.attachments.map(function (value) {
            FileUpload.removeAttachment(value.attachmentID);
        });
        // Removing previews
        $('.dropzone-previews .dz-preview ').each(function (index, item) {
            $(item).remove();
        });
        // Clearing Fileupload class attachments
        FileUpload.attachaments = [];
        // Clearing up the local storage object
        PostCreate.clearDraftData();
        // Clearing up the text area value
    },

    /**
     * Saves post draft data
     */
    saveDraftData: function(){
        Post.draftData.attachments = FileUpload.attachaments;
        Post.draftData.text = $('#dropzone-uploader').val();
        localStorage.setItem('draftData', JSON.stringify(Post.draftData));
    },

    /**
     * Clears up draft data
     * @param callback
     */
    clearDraftData: function(callback = null){
        localStorage.removeItem('draftData');
        Post.draftData = Post.initialDraftData;
        if(callback !== null){
            callback;
        }
        $('#dropzone-uploader').val(Post.draftData.text);
    },


    /**
     * Populates create/edit post form with draft data
     * @returns {boolean|any}
     */
    populateDraftData: function(){
        const draftData = localStorage.getItem('draftData');
        if(draftData){
            return JSON.parse(draftData);
        }
        else{
            return false;
        }
    },

    /**
     * Save new / update post
     * @param type
     * @param postID
     */
    save: function (type = 'create', postID = false, forceSave = false) {
        // Warning for any file that might still be uploading or a video transcoding
        if((FileUpload.isLoading === true || FileUpload.isTranscodingVideo === true) && forceSave === false){
            let dialogMessage = '';
            if(FileUpload.isLoading === true){
                dialogMessage = `${trans('Some attachments are still being uploaded.')} ${trans('Are you sure you want to continue?')}`;
            }
            if(FileUpload.isTranscodingVideo === true){
                dialogMessage = `${trans('A video is currently being converted.')} ${trans('Are you sure you want to continue without it?')}`;
            }
            $('#confirm-post-save .modal-body p').html(dialogMessage);
            $('.confirm-post-save').unbind('click');
            $('.confirm-post-save').on('click',function () {
                PostCreate.save(type, postID, true);
            });
            $('#confirm-post-save').modal('show');
            return false;
        }

        updateButtonState('loading',$('.post-create-button'));
        PostCreate.savePostScheduleSettings();
        let route = app.baseUrl + '/posts/save';
        let data = {
            'attachments': FileUpload.attachaments,
            'text': $('#dropzone-uploader').val(),
            'price': PostCreate.postPrice,
            'postNotifications' : PostCreate.postNotifications,
            'postReleaseDate': PostCreate.postReleaseDate,
            'postExpireDate': PostCreate.postExpireDate
        };
        if(type === 'create'){
            data.type = 'create';
        }
        else{
            data.type = 'update';
            data.id = postID;
        }
        $.ajax({
            type: 'POST',
            data: data,
            url: route,
            success: function () {
                if(type === 'create'){
                    PostCreate.isSavingRedirect = true;
                    PostCreate.clearDraftData(redirect(app.baseUrl+'/'+user.username));
                }
                else{
                    redirect(app.baseUrl+'/posts/'+postID+'/'+user.username);
                }
                updateButtonState('loaded',$('.post-create-button'), trans('Save'));
                $('#confirm-post-save').modal('hide');
            },
            error: function (result) {
                if(result.status === 422 || result.status === 500) {
                    $.each(result.responseJSON.errors, function (field, error) {
                        if (field === 'text') {
                            $('.post-invalid-feedback').html(trans_choice('Your post must contain more than 10 characters.',mediaSettings.max_post_description_size, {'num':mediaSettings.max_post_description_size}));
                            $('#dropzone-uploader').addClass('is-invalid');
                            $('#dropzone-uploader').focus();
                        }
                        if (field === 'attachments') {
                            $('.post-invalid-feedback').html(trans('Your post must contain at least one attachment.'));
                            $('#dropzone-uploader').addClass('is-invalid');
                            $('#dropzone-uploader').focus();
                        }
                        if (field === 'price') {
                            $('.post-invalid-feedback').html(result.responseJSON.message);
                            $('#dropzone-uploader').addClass('is-invalid');
                            $('#dropzone-uploader').focus();
                        }

                        if(field === 'permissions'){
                            launchToast('danger',trans('Error'),error);
                        }
                    });
                }
                else if(result.status === 403){
                    launchToast('danger',trans('Error'),'Post not found.');
                }
                $('#confirm-post-save').modal('hide');
                updateButtonState('loaded',$('.post-create-button'), trans('Save'));
            }
        });
    },

    /**
     * Shows the post scheduling modal
     */
    showPostScheduleSettings: function(){
        try {
            if (typeof $.fn !== 'undefined' && $.fn.modal) {
                $('#post-schedule-dialog').modal('show');
            } else {
                console.log('Bootstrap modal not available for scheduling, using fallback.');
                this.showModalFallback('#post-schedule-dialog');
            }
        } catch (e) {
            console.error('Error showing schedule dialog:', e);
            // Fallback method
            this.showModalFallback('#post-schedule-dialog');
        }
    },

    /**
     * Handles saving the post schedule settings
     */
    savePostScheduleSettings: function(){
        PostCreate.postReleaseDate = null;
        PostCreate.postExpireDate = null;

        // Check if release date input exists before accessing it
        if($('#release-date-input').length > 0 && $('#release-date-input').val().length > 0){
            PostCreate.postReleaseDate = $('#release-date-input').val();
            
            // Check if preview element exists
            if($('.release-date-preview').length > 0) {
                $('.release-date-preview').html(' • ' + $('#release-date-input').val());
            }
            
            // Check if clear button exists
            if($('.release-date-clear').length > 0 && $('.release-date-clear').hasClass('d-none')) {
                $('.release-date-clear').removeClass('d-none');
            }
        }
        else{
            // Check if preview element exists
            if($('.release-date-preview').length > 0) {
                $('.release-date-preview').html('');
            }
            
            // Check if clear button exists
            if($('.release-date-clear').length > 0 && !$('.release-date-clear').hasClass('d-none')) {
                $('.release-date-clear').addClass('d-none');
            }
        }

        // Check if expiry date input exists before accessing it
        if($('#expiry-date-input').length > 0 && $('#expiry-date-input').val().length > 0){
            PostCreate.postExpireDate = $('#expiry-date-input').val();
            
            // Check if preview element exists
            if($('.expire-date-preview').length > 0) {
                $('.expire-date-preview').html(' • ' + $('#expiry-date-input').val());
            }
            
            // Check if clear button exists
            if($('.expire-date-clear').length > 0 && $('.expire-date-clear').hasClass('d-none')) {
                $('.expire-date-clear').removeClass('d-none');
            }

            if(PostCreate.postPrice > 0){
                if($('#post-schedule-dialog').length > 0) {
                    try {
                        if (typeof $.fn !== 'undefined' && $.fn.modal) {
                            $('#post-schedule-dialog').modal('hide');
                        } else {
                            this.hideModalFallback('#post-schedule-dialog');
                        }
                    } catch (e) {
                        console.error('Error hiding schedule dialog:', e);
                    }
                }
                
                PostCreate.clearPostPrice();
                if(typeof launchToast === 'function') {
                    launchToast('danger',trans('Error'),trans('Posts having an expire date can not be price locked.'));
                }
                return false;
            }
        }
        else{
            // Check if preview element exists
            if($('.expire-date-preview').length > 0) {
                $('.expire-date-preview').html('');
            }
            
            // Check if clear button exists
            if($('.expire-date-clear').length > 0 && !$('.expire-date-clear').hasClass('d-none')) {
                $('.expire-date-clear').addClass('d-none');
            }
        }

        // Check if modal exists before attempting to hide it
        if($('#post-schedule-dialog').length > 0) {
            try {
                if (typeof $.fn !== 'undefined' && $.fn.modal) {
                    $('#post-schedule-dialog').modal('hide');
                } else {
                    this.hideModalFallback('#post-schedule-dialog');
                }
            } catch (e) {
                console.error('Error hiding schedule dialog:', e);
                this.hideModalFallback('#post-schedule-dialog');
            }
        }
    },

    /**
     * Clears post release date
     */
    clearReleaseDate: function(){
        $('#release-date-input').val('');
        $('.release-date-preview').html('');
        if(!$('.release-date-clear').hasClass('d-none')){
            $('.release-date-clear').addClass('d-none');
        }
        PostCreate.postReleaseDate = null;
    },

    /**
     * Clears post expire date
     */
    clearExpireDate: function(){
        $('#expiry-date-input').val('');
        $('.expire-date-preview').html('');
        if(!$('.expire-date-clear').hasClass('d-none')){
            $('.expire-date-clear').addClass('d-none');
        }
        PostCreate.postExpireDate = null;
    },

};
