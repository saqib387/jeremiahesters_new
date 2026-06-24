@extends('voyager::master')

@section('page_title', 'Edit Privacy Policy')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-lock"></i> Edit Privacy Policy
        </h1>
        <a href="{{ route('voyager.dashboard') }}" class="btn btn-warning btn-add-new">
            <i class="voyager-list"></i> <span>Back to Dashboard</span>
        </a>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form role="form" 
                              class="form-edit-add" 
                              action="{{ route('voyager.public-pages.update-privacy') }}" 
                              method="POST" 
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Page Title -->
                            <div class="form-group @if($errors->has('title')) has-error @endif">
                                <label class="control-label" for="title">Page Title</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="title" 
                                       placeholder="Page Title"
                                       value="{{ old('title', $privacyPage->title ?? '') }}">
                                @if($errors->has('title'))
                                    <span class="help-block">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <!-- Short Title -->
                            <div class="form-group @if($errors->has('short_title')) has-error @endif">
                                <label class="control-label" for="short_title">Short Title</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="short_title" 
                                       placeholder="Short Title (e.g., Privacy)"
                                       value="{{ old('short_title', $privacyPage->short_title ?? '') }}">
                                <small class="help-block">Short title used in navigation and links (e.g., "Privacy")</small>
                                @if($errors->has('short_title'))
                                    <span class="help-block">{{ $errors->first('short_title') }}</span>
                                @endif
                            </div>

                            <!-- Page Content -->
                            <div class="form-group @if($errors->has('content')) has-error @endif">
                                <label class="control-label" for="content">Page Content <span class="text-danger">*</span></label>
                                <textarea class="form-control richTextBox" 
                                          name="content" 
                                          placeholder="The main content of the privacy page"
                                          rows="20">{{ old('content', $privacyPage->content ?? '') }}</textarea>
                                <small class="help-block">The main content of the privacy page</small>
                                @if($errors->has('content'))
                                    <span class="help-block">{{ $errors->first('content') }}</span>
                                @endif
                            </div>

                            <!-- Read-only Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Page Slug</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="{{ $privacyPage->slug ?? 'privacy-policy' }}" 
                                               disabled>
                                        <small class="help-block">URL slug (read-only)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Last Updated</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="{{ $privacyPage->updated_at ? $privacyPage->updated_at->format('M d, Y H:i') : 'Never' }}" 
                                               disabled>
                                        <small class="help-block">Last modification date</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="voyager-check"></i> <span>Update Privacy Policy</span>
                            </button>
                        </form>

                        <iframe id="form_target" name="form_target" style="display:none"></iframe>
                        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                              enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                            <input name="image" id="upload_file" type="file"
                                   onchange="$('#my_form').submit();this.value='';">
                            <input type="hidden" name="type_slug" id="type_slug" value="public-pages">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower('Privacy Policy') }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_this_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name')
            };

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute found
            $('[data-toggle="datepicker"]').datepicker({
                todayBtn: "linked",
                clearBtn: true
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.media.delete') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop