<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        @csrf
        <div class="flex" x-data="{ selectedTheme: @entangle('theme_id') }">
            <div class="mx-3">
                <div class="form-group mb-7">
                    <div :class="{ 'ring-primary-500 bg-primary-500 text-white': selectedTheme == 1, 'ring-gray-200 bg-white text-gray-950': selectedTheme != 1 }"
                         class="theme-img-radio ring-1 p-1 rounded transition duration-75 hover:ring-gray-400 focus-within:ring-primary-500" data-id="1">
                        <input type="radio" wire:model="theme_configuration" name="theme_configuration" value="1" id="theme1" class="hidden" x-model="selectedTheme">
                        <label for="theme1" class="block cursor-pointer">
                            <img src="{{ asset('images/theme1.png') }}" alt="Template" class="transition duration-300 ease-in-out transform hover:scale-105">
                        </label>
                    </div>
                </div>
            </div>
            <div class="mx-3">
                <div class="form-group mb-7">
                    <div :class="{ 'ring-primary-500 bg-primary-500 text-white': selectedTheme == 2, 'ring-gray-200 bg-white text-gray-950': selectedTheme != 2 }"
                         class="theme-img-radio ring-1 p-1 rounded transition duration-75 hover:ring-gray-400 focus-within:ring-primary-500" data-id="2">
                        <input type="radio" wire:model="theme_configuration" name="theme_configuration" value="2" id="theme2" class="hidden" x-model="selectedTheme">
                        <label for="theme2" class="block cursor-pointer">
                            <img src="{{ asset('images/theme2.png') }}" alt="Template" class="transition duration-300 ease-in-out transform hover:scale-105">
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <x-filament-panels::form.actions :actions="$this->getFormActions()" />


            {{-- <x-filament::button class="px-6" type="submit">{{ __('messages.save') }}</x-filament::button> --}}
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
