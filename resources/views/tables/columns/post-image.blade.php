@php
    $image = $getRecord()->post_types == \App\Models\Post::VIDEO_TYPE_ACTIVE ? (!empty($getRecord()->postVideo->thumbnail_image_url) ? $getRecord()->postVideo->thumbnail_image_url : $getRecord()->postVideo->uploaded_thumb ?? '') : $getRecord()->post_image;
@endphp
<div class="d-flex align-items-center">
    <div class=" position-relative overflow-hidden width-custom">
        <a href="{{ $image }}" data-lightbox="image-{{$getRecord()->id}}">
            <img src="{{ $image }}" class="float-start  width-custom">
        </a>
    </div>
</div>
