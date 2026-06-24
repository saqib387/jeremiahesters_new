<div itemscope itemtype="http://schema.org/ImageGallery" class="h-100">
    <figure class="h-100" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" href="{{$attachment->path}}" {{--data-size="1600x1067"  data-med-size="1024x683" data-med="" data-author="" --}}>
    @if($isGallery)
            <div class="post-media-image" style="background-image: url('{{$attachment->path}}');" data-fallback-url="{{asset('img/default-post-image.jpg')}}">
            </div>
        @else
            <img src="{{$post->attachments[0]->path}}" draggable="false" alt="" class="img-fluid rounded-0 w-100" onerror="this.src='{{asset('img/default-post-image.jpg')}}'">
        @endif
    </figure>
</div>
