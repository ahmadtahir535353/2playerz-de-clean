{{-- <div>
    {{ $getRecord()->id }}
</div>
<div class="flex w-full py-5">
    <div style="--c-400:var(--success-400);--c-500:var(--success-500);--c-600:var(--success-600);" class="w-full bg-custom-600 rounded-full">
        <div class="bg-blue-600 text-xs font-medium text-blue-100 text-end p-0.5 leading-none rounded-full"
            style="width: 50%"> 45%</div>
    </div>
</div> --}}


{{-- <span class="badge {{App\Models\RssFeed::YES == $getRecord()->auto_update ?  'bg-success' : 'bg-danger'}}  fs-7 m-1">
    {{App\Models\RssFeed::AUTO_UPDATE[$getRecord()->auto_update]}}
</span>
<div>
    <x-filament::button wire:click="openNewUserModal({{$getRecord()->id}})">
        {{__('messages.sync')}}
    </x-filament::button>
</div> --}}

<div class="my-2 flex flex-col items-center justify-center">
    <x-filament::badge :color="App\Models\RssFeed::YES == $getRecord()->auto_update ? 'success' : 'danger'" style="width: 40px">
        {{ App\Models\RssFeed::YES == $getRecord()->auto_update ? __('messages.page.yes') : __('messages.page.no') }}
    </x-filament::badge>

    <div class="mt-2">
        <x-filament::button class="bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center "
            wire:click="openNewUserModal({{ $getRecord()->id }})">
            <div class="flex items-center">
                {{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg> --}}
                <span class="ms-1">{{ __('messages.sync') }}</span>
            </div>
        </x-filament::button>
    </div>
</div>
