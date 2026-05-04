import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/assets/front/scss/main.scss',
            'resources/theme/js/vendor.js',
            'resources/theme/js/plugins.js',
            // 'resources/assets/js/turbo.js',
            'resources/assets/js/web/custom.js',
            'resources/assets/js/front/gallery-page.js',
            'resources/assets/js/front/video-page.js',
            'resources/assets/js/front/audio.js',
            'resources/assets/js/front/home.js',
            'resources/assets/js/post-reaction/post_reaction.js',
            'resources/assets/js/custom/helpers.js',
            'resources/assets/css/audio.css',
            'resources/js/filament-seo-box.js'
        ]),
    ],
});
