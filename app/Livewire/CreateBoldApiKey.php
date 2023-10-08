<?php

namespace App\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

class CreateBoldApiKey extends Component
{
    #[Locked]
    public ?string $boldKeyId;

    #[Locked]
    public ?string $boldKeySecret;

    #[Rule('required|string|uuid')]
    public string $boldToken = '';

    public function mount()
    {
        $this->boldKeyId = auth()->user()->bold_key_id;
        $this->boldKeySecret = auth()->user()->bold_key_id; /* Prevent real secret from leaking */
    }

    public function createBoldApiKey()
    {
        $this->validate();

        $result = auth()->user()->createBoldApiKey($this->boldToken);

        $result ? $this->dispatch('created') : $this->dispatch('failed');

        $this->mount();
    }

    public function render()
    {
        return <<<'HTML'
            <x-form-section submit="createBoldApiKey">
                <x-slot name="title">
                    {{ __('Bold API Key') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Create Bold API Key using your Bold Bearer token.') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="boldKeyId" value="{{ __('Bold Key ID') }}" />
                        <x-input id="boldKeyId" type="text" class="mt-1 block w-full disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200" wire:model="boldKeyId" disabled />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="boldKeySecret" value="{{ __('Bold Key Secret') }}" />
                        <x-input id="boldKeySecret" type="password" class="mt-1 block w-full disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200" wire:model="boldKeySecret" disabled />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="boldToken" value="{{ __('Bold Bearer Token') }}" />
                        <x-input id="boldToken" type="password" class="mt-1 block w-full" wire:model="boldToken" />
                        <x-input-error for="boldToken" class="mt-2" />
                    </div>
                </x-slot>

                <x-slot name="actions">
                    <x-action-message class="mr-3 text-red-600" on="failed">
                        {{ __('Error, Bold Bearer token invalid.') }}
                    </x-action-message>

                    <x-action-message class="mr-3" on="created">
                        {{ __('Created.') }}
                    </x-action-message>

                    <x-button wire:loading.attr="disabled">
                        {{ __('Create') }}
                    </x-button>
                </x-slot>
            </x-form-section>
            HTML;
    }
}
