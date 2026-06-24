@extends('layouts.user-no-nav')

@section('page_title', __('Test Upload'))

@section('styles')
    <style>
        .test-image-container {
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .test-image {
            max-width: 300px;
            height: auto;
            display: block;
            margin: 10px 0;
        }
        .image-details {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h3 class="mt-4">Test Uploaded Images</h3>
            
            <div class="card">
                <div class="card-body">
                    <h5>Upload Test</h5>
                    <form id="test-upload-form" method="POST" action="{{ route('attachment.upload', ['type' => 'post']) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Select image to upload:</label>
                            <input type="file" name="file" id="test-file-input" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5>Recent Attachments</h5>
                    <div id="attachments-container">
                        @php
                            $attachments = \App\Model\Attachment::latest()->take(10)->get();
                        @endphp
                        
                        @foreach($attachments as $attachment)
                            <div class="test-image-container">
                                @if(strpos($attachment->type, 'image') !== false)
                                    <img src="{{ Storage::url($attachment->filename) }}" class="test-image" onerror="this.src='{{asset('img/default-post-image.jpg')}}'">
                                @elseif(strpos($attachment->type, 'video') !== false)
                                    <video controls class="test-image" src="{{ Storage::url($attachment->filename) }}"></video>
                                @endif
                                <div class="image-details">
                                    <p><strong>ID:</strong> {{ $attachment->id }}</p>
                                    <p><strong>Filename:</strong> {{ $attachment->filename }}</p>
                                    <p><strong>Path:</strong> {{ Storage::url($attachment->filename) }}</p>
                                    <p><strong>Type:</strong> {{ $attachment->type }}</p>
                                    <p><strong>File exists:</strong> {{ Storage::disk(config('filesystems.defaultFilesystemDriver'))->exists($attachment->filename) ? 'Yes' : 'No' }}</p>
                                    <p><strong>Post ID:</strong> {{ $attachment->post_id }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#test-upload-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.success) {
                        alert('Upload successful! Refreshing page...');
                        location.reload();
                    } else {
                        alert('Upload failed: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Upload error: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        });
    });
</script>
@endsection 