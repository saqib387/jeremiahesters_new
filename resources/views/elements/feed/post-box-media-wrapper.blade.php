@if( !(!$isGallery && AttachmentHelper::getAttachmentType($attachment->type) == 'video'))
    <a href="{{$attachment->path}}" rel="mswp" title="" class="no-long-press">
        @endif

        @if($isGallery)
            @if(AttachmentHelper::getAttachmentType($attachment->type) == 'image')
                <div class="post-media-image" style="background-image: url('{{$attachment->path}}');" data-fallback-url="{{asset('img/default-post-image.jpg')}}" data-path="{{ $attachment->path }}">
                </div>
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'video')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <video class="video-preview w-100" src="{{$attachment->path}}#t=0.001" controls controlsList="nodownload" preload="metadata" {!! ($attachment->has_thumbnail ? 'poster="'.$attachment->thumbnail.'"' : '') !!} onerror="this.poster='{{asset('img/default-post-image.jpg')}}'"></video>
                </div>
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'audio')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <audio class="video-preview w-75" src="{{$attachment->path}}#t=0.001" controls controlsList="nodownload" preload="metadata"></audio>
                </div>
            @endif
        @else
            @if(AttachmentHelper::getAttachmentType($attachment->type) == 'image')
                <img src="{{$attachment->path}}" draggable="false" alt="" class="img-fluid rounded-0 w-100" onerror="this.src='{{asset('img/default-post-image.jpg')}}'">
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'video')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <video class="video-preview w-100" src="{{$attachment->path}}#t=0.001" controls controlsList="nodownload" preload="metadata" {!! ($attachment->has_thumbnail ? 'poster="'.$attachment->thumbnail.'"' : '') !!} onerror="this.poster='{{asset('img/default-post-image.jpg')}}'"></video>
                </div>
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'audio')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <audio class="video-preview w-75" src="{{$attachment->path}}#t=0.001" controls controlsList="nodownload" preload="metadata"></audio>
                </div>
            @endif
        @endif

        @if( !(!$isGallery && AttachmentHelper::getAttachmentType($attachment->type) == 'video'))
    </a>
@endif

<script>
    // Add fallback for post-media-image background images
    document.addEventListener('DOMContentLoaded', function() {
        // Function to try alternate paths
        function tryAlternativePath(url) {
            // If URL contains post/images, try posts/images
            if (url.includes('post/images')) {
                return url.replace('post/images', 'posts/images');
            } 
            // If URL contains posts/images, try post/images
            else if (url.includes('posts/images')) {
                return url.replace('posts/images', 'post/images');
            }
            return url;
        }
        
        // Function for handling image load errors
        function handleImageLoadError(imgElement, originalUrl) {
            console.log('Image loading failed, trying alternatives for', originalUrl);
            
            // If we've already tried post/posts fix, try the next option
            if (imgElement.dataset.triedPostFix && imgElement.dataset.triedPostsFix) {
                // Both path fixes failed, use default image
                imgElement.src = '{{asset('img/default-post-image.jpg')}}';
                return;
            }
            
            // Try alternative paths
            let alternativeUrl;
            if (originalUrl.includes('posts/images') && !imgElement.dataset.triedPostFix) {
                imgElement.dataset.triedPostFix = true;
                alternativeUrl = originalUrl.replace('posts/images', 'post/images');
            } else if (originalUrl.includes('post/images') && !imgElement.dataset.triedPostsFix) {
                imgElement.dataset.triedPostsFix = true;
                alternativeUrl = originalUrl.replace('post/images', 'posts/images');
            } else {
                // If we can't fix paths, use default image
                imgElement.src = '{{asset('img/default-post-image.jpg')}}';
                return;
            }
            
            console.log('Trying alternative URL:', alternativeUrl);
            imgElement.src = alternativeUrl;
        }
        
        // Handle background images
        const postMediaImages = document.querySelectorAll('.post-media-image');
        postMediaImages.forEach(function(element) {
            const bgUrl = element.style.backgroundImage.replace(/url\(['"]?(.*?)['"]?\)/i, '$1');
            const fallbackUrl = element.getAttribute('data-fallback-url');
            const originalPath = element.getAttribute('data-path');
            
            if (bgUrl && fallbackUrl) {
                const img = new Image();
                img.onerror = function() {
                    // Try alternative path first
                    let alternativeUrl = tryAlternativePath(bgUrl);
                    if (alternativeUrl !== bgUrl) {
                        console.log('Trying alternative background URL:', alternativeUrl);
                        const altImg = new Image();
                        altImg.onerror = function() {
                            console.log('Alternative URL failed too, using fallback');
                            element.style.backgroundImage = `url('${fallbackUrl}')`;
                        };
                        altImg.onload = function() {
                            console.log('Alternative URL worked');
                            element.style.backgroundImage = `url('${alternativeUrl}')`;
                        };
                        altImg.src = alternativeUrl;
                    } else {
                        // No alternative, use fallback
                        element.style.backgroundImage = `url('${fallbackUrl}')`;
                    }
                };
                
                img.src = bgUrl;
            }
        });
        
        // Handle regular images
        const postImages = document.querySelectorAll('img');
        postImages.forEach(function(img) {
            // Skip images that already have error handling
            if (img.dataset.errorHandled) return;
            
            // Save original source
            const originalSrc = img.src;
            img.dataset.originalSrc = originalSrc;
            img.dataset.errorHandled = true;
            
            // Set up error handler
            img.onerror = function() {
                handleImageLoadError(this, this.dataset.originalSrc);
            };
        });
        
        // Handle videos too
        const videos = document.querySelectorAll('video');
        videos.forEach(function(video) {
            // Skip videos that already have error handling
            if (video.dataset.errorHandled) return;
            
            const originalSrc = video.src;
            video.dataset.originalSrc = originalSrc;
            video.dataset.errorHandled = true;
            
            // Error handler for video source
            video.onerror = function() {
                // Try alternative paths
                if (this.dataset.triedFixes) {
                    console.log('Video still not loading after path fixes');
                    return;
                }
                
                this.dataset.triedFixes = true;
                const alternativeUrl = tryAlternativePath(this.dataset.originalSrc);
                if (alternativeUrl !== this.dataset.originalSrc) {
                    console.log('Trying alternative video URL:', alternativeUrl);
                    this.src = alternativeUrl;
                }
            };
        });
    });
</script>
