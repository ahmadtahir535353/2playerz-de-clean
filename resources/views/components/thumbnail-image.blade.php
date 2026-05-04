<div>
    @if(!is_null($evaluate(fn ($get) => $get('thumbnail_image_url'))))
    <img src="{{ $evaluate(fn ($get) => $get('thumbnail_image_url')) }}" alt="Thumbnail Image" style="max-width: 100%; height: auto;">
    <!-- Debugging: Display the URL -->
    {{-- <p>{{ $thumbnailImage }}</p> --}}
{{-- @else
    <p>No image to display</p> --}}
@endif
</div>
