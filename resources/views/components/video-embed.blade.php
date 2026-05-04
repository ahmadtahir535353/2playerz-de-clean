@if($evaluate(fn ($get) => $get('video_embed_code')))
<div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; height: auto;">
    <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" width="560" height="315" src="{{ $evaluate(fn ($get) => $get('video_embed_code')) }}" frameborder="0" allowfullscreen></iframe>
</div>
@endif

