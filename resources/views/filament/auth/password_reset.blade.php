<div>
    <script>
        // Get email from sessionStorage and fill in form
        document.addEventListener('DOMContentLoaded', function() {
            const email = sessionStorage.getItem('forgot_password_email');
            if (email) {
                // Wait for Livewire to be ready
                setTimeout(function() {
                    @this.set('data.email', email);
                    sessionStorage.removeItem('forgot_password_email');
                }, 100);
            }
        });
    </script>
    <x-filament-panels::form wire:submit="request">
        <div class="w-full pb-3 text-center relative flex flex-col items-center">
            <a href="{{ route('front.home') }}" data-turbo="false"
                class="text-decoration-none sidebar-logo flex items-center" target="_blank" title="InfyNews">
                <div class="image image-mini">
                    <img src="{{ getAppLogo() }}" class="me-4" alt="InfyNews-logo" width="100px" height="30px">
                </div>
            </a>
            <h1
                class="fi-simple-header-heading text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                {{ __('messages.forgot_password') }}?
            </h1>

            <p class="fi-simple-header-subheading mt-2 text-center text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('filament.auth.auth.login') }}"
                    class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-md fi-link-size-md gap-1.5 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action">
                    <span
                        class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline custom-signup-link"
                        style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                        <span class="text-lg mr-5"><- </span> {{ __('messages.other_lang.back_to_login') }}
                        </span>
                </a>
            </p>
        </div>
        {{ $this->form }}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />

    </x-filament-panels::form>
</div>
