<textarea name="{{ $getStatePath() }}" {{ $applyStateBindingModifiers('wire:model') }} id="custom-tinyeditor">{{ $getState() }}</textarea>
<script>
    tinymce.init({
        selector: '#custom-tinyeditor',
        plugins: 'code',
        toolbar: 'code | bold italic',
        setup: function (editor) {
            editor.on('init', function () {
                editor.setContent('<div class="info-box">Default custom content</div>');
            });
        },
        content_css: '{{ asset('assets/css/tinymce-custom.css') }}',
        style_formats: [
            { title: 'Info Box', block: 'div', classes: 'info-box' }
        ]
    });
</script>