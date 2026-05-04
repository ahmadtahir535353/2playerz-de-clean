@extends('customer-panel.layout.main')
@section('title', __('messages.edit_profile.edit_profile'))  {{-- "Edit Profile" --}}
@section('content')
<style>
    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .max-w-xs {
        max-width: 16rem;
    }
</style>
<div class="bg-[#F5F5F5] dark:bg-[#18171C] mb-4 p-3 text-end rounded-md">
    <h3 class="text-xl opacity-60 tracking-wider">{{ __('messages.edit_profile.edit_profile')}}</h3>
</div>
<form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="w-full bg-[#F5F5F5] dark:bg-[#18171C] p-6 rounded-md shadow-md space-y-6">
    @csrf
    @method('PUT')
    @if (session('success'))
    <div class="bg-green-600 text-white p-4 rounded-lg mb-4 shadow-md border border-green-700 flex items-center space-x-2">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.edit_profile.avtar') }}</label>

            <input type="file" name="avatar" id="avatarInput" class="hidden" accept="image/*" />

            <div class="flex items-center space-x-2">
                <button type="button" id="customBrowseBtn" class="text-sm py-2 px-4 rounded border-0 font-semibold bg-purple-700 text-white hover:bg-purple-800">
                    {{ __('messages.other_lang.choose_file') }}
                </button>
                <span id="fileStatus" class="text-sm text-gray-400 truncate max-w-xs flex-1">
                    {{ $customer->avatar ? __('messages.other_lang.no_file_chosen') : __('messages.other_lang.no_file_selected') }}
                </span>
                <!-- Preview Image -->
                <div id="imagePreview" class="ml-auto hidden">
                    <img id="previewImg" src="#" alt="Preview" class="w-16 h-16 rounded-full object-cover" />
                </div>
            </div>

            @if($customer->avatar)
            <div class="mt-2">
                <a href="{{ $customer->profile_image ?? asset('web/media/avatars/150-2.jpg') }}" target="_blank">
                    <img src="{{ $customer->profile_image ?? asset('web/media/avatars/150-2.jpg') }}" alt="Current Avatar" class="w-16 h-16 rounded-full object-cover cursor-pointer">
                </a>
            </div>
            @endif

        </div>


        <!-- Email -->
        <div class="md:col-span-1">
            <label class="block font-semibold mb-2">{{ __('messages.edit_profile.email')}}:</label>
            <input type="email" name="email" value="{{ $customer->email }}" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]" />
        </div>

        <!-- Password -->
        <div class="md:col-span-1">
            <label class="block font-semibold mb-2">{{ __('messages.edit_profile.password')}}: <span class="text-red-500">*</span></label>
            <input type="password" name="password" value="" placeholder="{{ __('messages.edit_profile.password_placeholder')}}" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]" />
        </div>
        <div class="md:col-span-1">
            <label class="block font-semibold mb-2">{{ __('messages.other_lang.username_edit')}}:</label>
            <input type="text" 
                name="username" 
                value="{{ $customer->username }}" 
                class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]"
                {{ $customer->is_username_edit ? 'readonly disabled' : '' }} />
        </div>
    </div>

    <!-- About Me -->
    <div>
        <label class="block font-semibold mb-2">{{ __('messages.customer_profile.about_me')}}:</label>
        <textarea name="about_me" rows="4" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->about_me }}</textarea>
    </div>

    <!-- Location and Occupation -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.loction')}}:</label>
            <input type="text" name="location" value="{{ $customer->location }}" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]" />
        </div>
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.occupation')}}:</label>
            <input type="text" name="occupation" value="{{ $customer->occupation }}" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]" />
        </div>
    </div>

    <!-- Interests (replacing Consoles) and Accessories -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.consoles')}}:</label>
            <textarea name="consoles" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->consoles }}</textarea>
        </div>
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.accessories')}}:</label>
            <textarea name="accessories" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->accessories ?? '' }}</textarea>
        </div>
    </div>

    <!-- Games and Genres -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.favourite_games')}}:</label>
            <textarea name="favorite_games" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->favorite_games }}</textarea>
        </div>
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.favourite_genre')}}:</label>
            <textarea name="favorite_genre" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->favorite_genre }}</textarea>
        </div>
    </div>

    <!-- Series and Films -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.edit_profile.series')}}:</label>
            <textarea name="favorite_series" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->favorite_series }}</textarea>
        </div>
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.edit_profile.movies')}}:</label>
            <textarea name="favorite_films" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->favorite_films }}</textarea>
        </div>
    </div>

    <!-- Music and Hobbies -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.edit_profile.music')}}:</label>
            <textarea name="favorite_music" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->favorite_music }}</textarea>
        </div>
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.hobbies')}}:</label>
            <textarea name="hobbies" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]">{{ $customer->hobbies }}</textarea>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block font-semibold mb-2">PSN ID:</label>
            <input type="text" name="psn_id" value="{{ $customer->psn_id }}" placeholder="{{ __('messages.other_lang.psn_placeholder')}}" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]" />
        </div>
        <div>
            <label class="block font-semibold mb-2">Xbox Live ID:</label>
            <input type="text" name="xbox_live_id" value="{{ $customer->xbox_live_id }}" placeholder="{{ __('messages.other_lang.xbox_placeholder')}}" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]" />
        </div>
    </div>

    <!-- Motto and Theme Controls -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
        <div>
            <label class="block font-semibold mb-2">{{ __('messages.customer_profile.my_motto')}}:</label>
            <input type="text" name="my_motto" value="{{ $customer->my_motto }}" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96]" />
        </div>
        <!-- Private Message Settings -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('messages.other_lang.private_message_settings')}}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-2 text-gray-900 dark:text-white">{{ __('messages.other_lang.who_can_send_me_private_messages')}}</label>
                    <select name="who_can_send_messages" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96] border border-gray-300 dark:border-gray-600">
                        <option value="all" {{ $customer->who_can_send_messages == 'all' ? 'selected' : '' }}>{{ __('messages.other_lang.all_members')}}</option>
                        <option value="following" {{ $customer->who_can_send_messages == 'following' ? 'selected' : '' }}>{{ __('messages.other_lang.members_i_follow')}}</option>
                        <option value="nobody" {{ $customer->who_can_send_messages == 'nobody' ? 'selected' : '' }}>{{ __('messages.other_lang.nobody')}}</option>
                    </select>
                </div>
                
                <div>
                    <label class="block font-semibold mb-2 text-gray-900 dark:text-white">{{ __('messages.other_lang.how_i_want_to_be_notified')}}</label>
                    <select name="message_notification_preference" class="w-full bg-white dark:bg-black rounded px-4 py-2 focus:outline-none focus:ring focus:ring-[#734E96] border border-gray-300 dark:border-gray-600">
                        <option value="notification_only" {{ $customer->message_notification_preference == 'notification_only' ? 'selected' : '' }}>{{ __('messages.other_lang.only_notification_on_website')}}</option>
                        <option value="email_and_notification" {{ $customer->message_notification_preference == 'email_and_notification' ? 'selected' : '' }}>{{ __('messages.other_lang.email_and_notification')}}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end items-center space-x-4 mt-4 md:mt-0">
            <button type="button" id="dark-toggle" class="bg-gray-700 px-3 py-2 rounded text-white {{ $customer->theme == 'dark' ? 'bg-purple-600' : '' }}">
                🌙
            </button>
            <button type="button" id="light-toggle" class="bg-gray-700 px-3 py-2 rounded text-white {{ $customer->theme == 'light' ? 'bg-purple-600' : '' }}">
                ☀️
            </button>
            <input type="hidden" name="theme" id="theme-input" value="{{ $customer->theme }}">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-5 py-2 rounded text-white">
                {{ __('messages.edit_profile.save')}}
            </button>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkToggle = document.getElementById('dark-toggle');
        const lightToggle = document.getElementById('light-toggle');
        const themeInput = document.getElementById('theme-input');

        const theme = localStorage.getItem("theme")
        themeInput.value = theme

        darkToggle.addEventListener('click', function() {
            themeInput.value = 'dark';
            localStorage.setItem("theme", "dark")
            document.querySelector('html').classList.add('dark');
            darkToggle.classList.add('bg-purple-600');
            lightToggle.classList.remove('bg-purple-600');
        });

        lightToggle.addEventListener('click', function() {
            themeInput.value = 'light';
            localStorage.setItem("theme", "light")
            document.querySelector('html').classList.remove('dark');
            lightToggle.classList.add('bg-purple-600');
            darkToggle.classList.remove('bg-purple-600');
        });

        document.querySelector('html').classList.add(theme);
        if (themeInput.value === 'dark') {
            // document.querySelector('html').classList.add('dark');
            darkToggle.classList.add('bg-purple-600');
        } else {
            lightToggle.classList.add('bg-purple-600');
        }
    });

    $(document).ready(function () {
        $('#customBrowseBtn').on('click', function () {
            $('#avatarInput').trigger('click');
        });

        $('#avatarInput').on('change', function () {
            const file = this.files[0];
            const fileName = file ? file.name : '{{ __('messages.other_lang.no_file_selected') }}';
            $('#fileStatus').text(fileName);

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#imagePreview').removeClass('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').addClass('hidden');
            }
        });
    });
</script>
@endsection