<x-filament-panels::page>
    <x-filament-panels::form wire:submit="savePart1">
        {{ $this->getFormSchemaPart1 }}
        <x-filament-panels::form.actions :actions="$this->getFormAction1()" />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit.prevent="savePart2">
        {{ $this->getFormSchemaPart2 }}
        <x-filament-panels::form.actions :actions="$this->getFormAction2()" />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit.prevent="savePart3">
        {{ $this->getFormSchemaPart3 }}
        <x-filament-panels::form.actions :actions="$this->getFormAction3()" />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit.prevent="savePart4">
        {{ $this->getFormSchemaPart4 }}
        <x-filament-panels::form.actions :actions="$this->getFormAction4()" />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit.prevent="savePart5">
        {{ $this->getFormSchemaPart5 }}
        <x-filament-panels::form.actions :actions="$this->getFormAction5()" />
    </x-filament-panels::form>
</x-filament-panels::page>
