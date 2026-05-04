@php
use App\Models\Post;
@endphp
@if (auth()->user()->hasRole('customer'))
<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 justify-center">
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.customer.resources.posts.create', ['section' => Post::ARTICLE]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-file-lines icon-article text-white mx-auto mb-5" width="50"
                            height="50" aria-hidden="true" focusable="false" data-prefix="far" data-icon="file-lines"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M365.3 93.38l-74.63-74.64C278.6 6.742 262.3 0 245.4 0L64-.0001c-35.35 0-64 28.65-64 64l.0065 384c0 35.34 28.65 64 64 64H320c35.2 0 64-28.8 64-64V138.6C384 121.7 377.3 105.4 365.3 93.38zM336 448c0 8.836-7.164 16-16 16H64.02c-8.838 0-16-7.164-16-16L48 64.13c0-8.836 7.164-16 16-16h160L224 128c0 17.67 14.33 32 32 32h79.1V448zM96 280C96 293.3 106.8 304 120 304h144C277.3 304 288 293.3 288 280S277.3 256 264 256h-144C106.8 256 96 266.8 96 280zM264 352h-144C106.8 352 96 362.8 96 376s10.75 24 24 24h144c13.25 0 24-10.75 24-24S277.3 352 264 352z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.article') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.article_with_images') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.customer.resources.posts.create', ['section' => Post::GALLERY]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-images icon-article text-white mx-auto mb-5" width="50"
                            height="50" aria-hidden="true" focusable="false" data-prefix="far" data-icon="images"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M512 32H160c-35.35 0-64 28.65-64 64v224c0 35.35 28.65 64 64 64H512c35.35 0 64-28.65 64-64V96C576 60.65 547.3 32 512 32zM528 320c0 8.822-7.178 16-16 16h-16l-109.3-160.9C383.7 170.7 378.7 168 373.3 168c-5.352 0-10.35 2.672-13.31 7.125l-62.74 94.11L274.9 238.6C271.9 234.4 267.1 232 262 232c-5.109 0-9.914 2.441-12.93 6.574L176 336H160c-8.822 0-16-7.178-16-16V96c0-8.822 7.178-16 16-16H512c8.822 0 16 7.178 16 16V320zM224 112c-17.67 0-32 14.33-32 32s14.33 32 32 32c17.68 0 32-14.33 32-32S241.7 112 224 112zM456 480H120C53.83 480 0 426.2 0 360v-240C0 106.8 10.75 96 24 96S48 106.8 48 120v240c0 39.7 32.3 72 72 72h336c13.25 0 24 10.75 24 24S469.3 480 456 480z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.gallery') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.collection_of_images') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.customer.resources.posts.create', ['section' => Post::SORT_LIST]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-list-ol icon-article text-white mx-auto mb-5" width="50"
                            height="50" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="list-ol"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M55.1 56.04C55.1 42.78 66.74 32.04 79.1 32.04H111.1C125.3 32.04 135.1 42.78 135.1 56.04V176H151.1C165.3 176 175.1 186.8 175.1 200C175.1 213.3 165.3 224 151.1 224H71.1C58.74 224 47.1 213.3 47.1 200C47.1 186.8 58.74 176 71.1 176H87.1V80.04H79.1C66.74 80.04 55.1 69.29 55.1 56.04V56.04zM118.7 341.2C112.1 333.8 100.4 334.3 94.65 342.4L83.53 357.9C75.83 368.7 60.84 371.2 50.05 363.5C39.26 355.8 36.77 340.8 44.47 330.1L55.59 314.5C79.33 281.2 127.9 278.8 154.8 309.6C176.1 333.1 175.6 370.5 153.7 394.3L118.8 432H152C165.3 432 176 442.7 176 456C176 469.3 165.3 480 152 480H64C54.47 480 45.84 474.4 42.02 465.6C38.19 456.9 39.9 446.7 46.36 439.7L118.4 361.7C123.7 355.9 123.8 347.1 118.7 341.2L118.7 341.2zM512 64C529.7 64 544 78.33 544 96C544 113.7 529.7 128 512 128H256C238.3 128 224 113.7 224 96C224 78.33 238.3 64 256 64H512zM512 224C529.7 224 544 238.3 544 256C544 273.7 529.7 288 512 288H256C238.3 288 224 273.7 224 256C224 238.3 238.3 224 256 224H512zM512 384C529.7 384 544 398.3 544 416C544 433.7 529.7 448 512 448H256C238.3 448 224 433.7 224 416C224 398.3 238.3 384 256 384H512z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.sort_list') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.list_based_article') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.customer.resources.posts.create', ['section' => Post::OPEN_AI]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-robot text-white mx-auto mb-5" width="50 " height="50"
                            style="font-size: 48px;" aria-hidden="true" focusable="false" data-prefix="fas"
                            data-icon="robot" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                            data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M9.375 233.4C3.375 239.4 0 247.5 0 256v128c0 8.5 3.375 16.62 9.375 22.62S23.5 416 32 416h32V224H32C23.5 224 15.38 227.4 9.375 233.4zM464 96H352V32c0-17.62-14.38-32-32-32S288 14.38 288 32v64H176C131.8 96 96 131.8 96 176V448c0 35.38 28.62 64 64 64h320c35.38 0 64-28.62 64-64V176C544 131.8 508.3 96 464 96zM256 416H192v-32h64V416zM224 296C201.9 296 184 278.1 184 256S201.9 216 224 216S264 233.9 264 256S246.1 296 224 296zM352 416H288v-32h64V416zM448 416h-64v-32h64V416zM416 296c-22.12 0-40-17.88-40-40S393.9 216 416 216S456 233.9 456 256S438.1 296 416 296zM630.6 233.4C624.6 227.4 616.5 224 608 224h-32v192h32c8.5 0 16.62-3.375 22.62-9.375S640 392.5 640 384V256C640 247.5 636.6 239.4 630.6 233.4z">
                            </path>
                        </svg>

                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.open_ai') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.article_with_open_ai') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.customer.resources.posts.create', ['section' => Post::VIDEO]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-circle-play icon-article text-white mx-auto mb-5" width="50 "
                            height="50" aria-hidden="true" focusable="false" data-prefix="fas"
                            data-icon="circle-play" role="img" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM176 168V344C176 352.7 180.7 360.7 188.3 364.9C195.8 369.2 205.1 369 212.5 364.5L356.5 276.5C363.6 272.1 368 264.4 368 256C368 247.6 363.6 239.9 356.5 235.5L212.5 147.5C205.1 142.1 195.8 142.8 188.3 147.1C180.7 151.3 176 159.3 176 168V168z">
                            </path>
                        </svg>

                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.video') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.upload_or_embed_videos') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.customer.resources.posts.create', ['section' => Post::AUDIO]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-music icon-article text-white mx-auto mb-5" width="50 "
                            height="50" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="music"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M511.1 367.1c0 44.18-42.98 80-95.1 80s-95.1-35.82-95.1-79.1c0-44.18 42.98-79.1 95.1-79.1c11.28 0 21.95 1.92 32.01 4.898V148.1L192 224l-.0023 208.1C191.1 476.2 149 512 95.1 512S0 476.2 0 432c0-44.18 42.98-79.1 95.1-79.1c11.28 0 21.95 1.92 32 4.898V126.5c0-12.97 10.06-26.63 22.41-30.52l319.1-94.49C472.1 .6615 477.3 0 480 0c17.66 0 31.97 14.34 32 31.99L511.1 367.1z">
                            </path>
                        </svg>

                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.audio') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.upload_audios_and_create_playlist') }}</p>
                </div>
            </a>
        </div>

        <!-- Repeat the above structure for each column -->

    </div>

</x-filament-panels::page>
@else
<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 justify-center">
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.admin.resources.posts.create', ['section' => Post::ARTICLE]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-file-lines icon-article text-white mx-auto mb-5" width="50"
                            height="50" aria-hidden="true" focusable="false" data-prefix="far" data-icon="file-lines"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M365.3 93.38l-74.63-74.64C278.6 6.742 262.3 0 245.4 0L64-.0001c-35.35 0-64 28.65-64 64l.0065 384c0 35.34 28.65 64 64 64H320c35.2 0 64-28.8 64-64V138.6C384 121.7 377.3 105.4 365.3 93.38zM336 448c0 8.836-7.164 16-16 16H64.02c-8.838 0-16-7.164-16-16L48 64.13c0-8.836 7.164-16 16-16h160L224 128c0 17.67 14.33 32 32 32h79.1V448zM96 280C96 293.3 106.8 304 120 304h144C277.3 304 288 293.3 288 280S277.3 256 264 256h-144C106.8 256 96 266.8 96 280zM264 352h-144C106.8 352 96 362.8 96 376s10.75 24 24 24h144c13.25 0 24-10.75 24-24S277.3 352 264 352z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.article') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.article_with_images') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.admin.resources.posts.create', ['section' => Post::GALLERY]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-images icon-article text-white mx-auto mb-5" width="50"
                            height="50" aria-hidden="true" focusable="false" data-prefix="far" data-icon="images"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M512 32H160c-35.35 0-64 28.65-64 64v224c0 35.35 28.65 64 64 64H512c35.35 0 64-28.65 64-64V96C576 60.65 547.3 32 512 32zM528 320c0 8.822-7.178 16-16 16h-16l-109.3-160.9C383.7 170.7 378.7 168 373.3 168c-5.352 0-10.35 2.672-13.31 7.125l-62.74 94.11L274.9 238.6C271.9 234.4 267.1 232 262 232c-5.109 0-9.914 2.441-12.93 6.574L176 336H160c-8.822 0-16-7.178-16-16V96c0-8.822 7.178-16 16-16H512c8.822 0 16 7.178 16 16V320zM224 112c-17.67 0-32 14.33-32 32s14.33 32 32 32c17.68 0 32-14.33 32-32S241.7 112 224 112zM456 480H120C53.83 480 0 426.2 0 360v-240C0 106.8 10.75 96 24 96S48 106.8 48 120v240c0 39.7 32.3 72 72 72h336c13.25 0 24 10.75 24 24S469.3 480 456 480z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.gallery') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.collection_of_images') }}</p>
                </div>
            </a>
        </div>
        <!-- <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.admin.resources.posts.create', ['section' => Post::SORT_LIST]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-list-ol icon-article text-white mx-auto mb-5" width="50"
                            height="50" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="list-ol"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M55.1 56.04C55.1 42.78 66.74 32.04 79.1 32.04H111.1C125.3 32.04 135.1 42.78 135.1 56.04V176H151.1C165.3 176 175.1 186.8 175.1 200C175.1 213.3 165.3 224 151.1 224H71.1C58.74 224 47.1 213.3 47.1 200C47.1 186.8 58.74 176 71.1 176H87.1V80.04H79.1C66.74 80.04 55.1 69.29 55.1 56.04V56.04zM118.7 341.2C112.1 333.8 100.4 334.3 94.65 342.4L83.53 357.9C75.83 368.7 60.84 371.2 50.05 363.5C39.26 355.8 36.77 340.8 44.47 330.1L55.59 314.5C79.33 281.2 127.9 278.8 154.8 309.6C176.1 333.1 175.6 370.5 153.7 394.3L118.8 432H152C165.3 432 176 442.7 176 456C176 469.3 165.3 480 152 480H64C54.47 480 45.84 474.4 42.02 465.6C38.19 456.9 39.9 446.7 46.36 439.7L118.4 361.7C123.7 355.9 123.8 347.1 118.7 341.2L118.7 341.2zM512 64C529.7 64 544 78.33 544 96C544 113.7 529.7 128 512 128H256C238.3 128 224 113.7 224 96C224 78.33 238.3 64 256 64H512zM512 224C529.7 224 544 238.3 544 256C544 273.7 529.7 288 512 288H256C238.3 288 224 273.7 224 256C224 238.3 238.3 224 256 224H512zM512 384C529.7 384 544 398.3 544 416C544 433.7 529.7 448 512 448H256C238.3 448 224 433.7 224 416C224 398.3 238.3 384 256 384H512z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.sort_list') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.list_based_article') }}</p>
                </div>
            </a>
        </div> -->
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.admin.resources.posts.create', ['section' => Post::LIVETICKER]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-bolt text-white mx-auto mb-5" width="50" height="50" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bolt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path fill="currentColor" d="M296 160H180.3L224 16.6C227.7 4.1 219.2-8 206.3-8c-4.375 0-8.75 1.438-12.38 4.375l-192 160C-7.25 161.8-9.5 176.1-2.625 186.8C1.125 193 8.25 197.4 16 197.4H131.7L88 340.8C84.25 353.3 92.75 365.4 105.7 365.4c4.375 0 8.75-1.438 12.38-4.375l192-160C327.3 191.6 329.5 177.3 322.6 166.6C318.9 160.4 311.8 156 304 156z"></path>
                        </svg>
                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.other_lang.liveticker') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.other_lang.real_time_updates') }}</p>
                </div>
            </a>
        </div>

        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.admin.resources.posts.create', ['section' => Post::OPEN_AI]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-robot text-white mx-auto mb-5" width="50 " height="50"
                            style="font-size: 48px;" aria-hidden="true" focusable="false" data-prefix="fas"
                            data-icon="robot" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                            data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M9.375 233.4C3.375 239.4 0 247.5 0 256v128c0 8.5 3.375 16.62 9.375 22.62S23.5 416 32 416h32V224H32C23.5 224 15.38 227.4 9.375 233.4zM464 96H352V32c0-17.62-14.38-32-32-32S288 14.38 288 32v64H176C131.8 96 96 131.8 96 176V448c0 35.38 28.62 64 64 64h320c35.38 0 64-28.62 64-64V176C544 131.8 508.3 96 464 96zM256 416H192v-32h64V416zM224 296C201.9 296 184 278.1 184 256S201.9 216 224 216S264 233.9 264 256S246.1 296 224 296zM352 416H288v-32h64V416zM448 416h-64v-32h64V416zM416 296c-22.12 0-40-17.88-40-40S393.9 216 416 216S456 233.9 456 256S438.1 296 416 296zM630.6 233.4C624.6 227.4 616.5 224 608 224h-32v192h32c8.5 0 16.62-3.375 22.62-9.375S640 392.5 640 384V256C640 247.5 636.6 239.4 630.6 233.4z">
                            </path>
                        </svg>

                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.open_ai') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.article_with_open_ai') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.admin.resources.posts.create', ['section' => Post::VIDEO]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-circle-play icon-article text-white mx-auto mb-5" width="50 "
                            height="50" aria-hidden="true" focusable="false" data-prefix="fas"
                            data-icon="circle-play" role="img" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM176 168V344C176 352.7 180.7 360.7 188.3 364.9C195.8 369.2 205.1 369 212.5 364.5L356.5 276.5C363.6 272.1 368 264.4 368 256C368 247.6 363.6 239.9 356.5 235.5L212.5 147.5C205.1 142.1 195.8 142.8 188.3 147.1C180.7 151.3 176 159.3 176 168V168z">
                            </path>
                        </svg>

                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.video') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.upload_or_embed_videos') }}</p>
                </div>
            </a>
        </div>
        <div class="col-span-1">
            <a class="block text-active-primary" href="{{ route('filament.admin.resources.posts.create', ['section' => Post::AUDIO]) }}">
                <div class="bg-primary-600 rounded-lg shadow-lg p-6">
                    <div class="item-icon">
                        <svg class="svg-inline--fa fa-music icon-article text-white mx-auto mb-5" width="50 "
                            height="50" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="music"
                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M511.1 367.1c0 44.18-42.98 80-95.1 80s-95.1-35.82-95.1-79.1c0-44.18 42.98-79.1 95.1-79.1c11.28 0 21.95 1.92 32.01 4.898V148.1L192 224l-.0023 208.1C191.1 476.2 149 512 95.1 512S0 476.2 0 432c0-44.18 42.98-79.1 95.1-79.1c11.28 0 21.95 1.92 32 4.898V126.5c0-12.97 10.06-26.63 22.41-30.52l319.1-94.49C472.1 .6615 477.3 0 480 0c17.66 0 31.97 14.34 32 31.99L511.1 367.1z">
                            </path>
                        </svg>

                    </div>
                    <div class="text-center mb-5 font-bold text-white">
                        {{ __('messages.post.audio') }}
                    </div>
                    <p class="text-center text-muted mb-0 text-white">{{ __('messages.post.upload_audios_and_create_playlist') }}</p>
                </div>
            </a>
        </div>

        <!-- Repeat the above structure for each column -->

    </div>

</x-filament-panels::page>
@endif
