<div>
    <x-filament-panels::form wire:submit="resetPassword">
        <div class="w-full pb-2 text-center relative flex flex-col items-center">
            <a href="{{ route('front.home') }}" data-turbo="false"
                class="text-decoration-none sidebar-logo flex items-center" target="_blank" title="InfyNews">
                <div class="image image-mini">
                    <img src="{{ getAppLogo() }}" class="me-4" alt="InfyNews-logo" width="40px" height="30px">
                </div>

            </a>
            <h1
                class="fi-simple-header-heading text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                {{ __('messages.reset') . ' ' . __('messages.staff.password') }}
            </h1>
        </div>
        {{ $this->form }}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
    </x-filament-panels::form>
</div>
