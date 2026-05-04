<x-filament-panels::page>

    <div class="grid flex-1 auto-cols-fr gap-y-8">
        <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(3, minmax(0, 1fr));"
            class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
            <div style="--col-span-default: span 2 / span 2;" class="col-[--col-span-default]">
                <x-filament-panels::form wire:submit="savePart1">
                    {{ $this->getFormSchemaPart1 }}
                    <x-filament-panels::form.actions :actions="$this->getFormAction1()" />
                </x-filament-panels::form>
            </div>
            <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default]">
                <x-filament-panels::form wire:submit="savePart2" style="margin-bottom:40px">
                    {{ $this->getFormSchemaPart2 }}
                    <x-filament-panels::form.actions :actions="$this->getFormAction2()" />
                </x-filament-panels::form>

                <x-filament-panels::form wire:submit="savePart3">
                    {{ $this->getFormSchemaPart3 }}
                    <x-filament-panels::form.actions :actions="$this->getFormAction3()" />
                </x-filament-panels::form>
            </div>
        </div>
    </div>

</x-filament-panels::page>
