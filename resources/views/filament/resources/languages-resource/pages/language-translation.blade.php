<x-filament-panels::page>
    <x-filament-panels::form wire:submit.prevent="save">
        {{ $this->form }}
        {{-- <div>
            <x-filament::button class="px-6" type="submit">{{ __('messages.common.save') }}</x-filament::button>
        </div> --}}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
        {{-- <pre>{{ print_r($this->form->getState(), true) }}</pre> --}}
    </x-filament-panels::form>
</x-filament-panels::page>
