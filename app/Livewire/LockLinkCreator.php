<?php

namespace App\Livewire;

use App\Enums\Lock;
use App\Rules\Expiration;
use Livewire\Attributes\Rule;
use Livewire\Component;

class LockLinkCreator extends Component
{
    #[Rule('required|string|min:3|max:32')]
    public string $name = '';

    #[Rule(['required', new Expiration])]
    public string $expiration = '24 hours';

    public string $link = '';
    public bool $displayingLink = false;

    public function createLink()
    {
        $this->validate();

        $this->link = Lock::createLink(
            owner: auth()->user(),
            causer: $this->name,
            expiration: $this->expiration
        );

        $this->displayingLink = true;
    }

    public function render()
    {
        return <<<'HTML'
            <div>
                <x-form-section submit="createLink">
                    <x-slot name="title">
                        {{ __('Link Generator') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('Genereer een link om tijdelijke toegang tot uw deuren te delen.') }}
                    </x-slot>

                    <x-slot name="form">
                        <div class="col-span-6 sm:col-span-4">
                            <x-label for="name" value="{{ __('Naam van uitgenodigde') }}" />
                            <x-input id="name" type="text" class="mt-1 block w-full" wire:model.blur="name" required />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <x-label for="expiration" value="{{ __('Geldigheidsduur van uitnodiging') }}" />
                            <x-input id="expiration" type="text" class="mt-1 block w-full" wire:model.blur="expiration"
                            required />
                            <x-input-error for="expiration" class="mt-2" />
                        </div>
                    </x-slot>

                    <x-slot name="actions">
                        <x-button wire:loading.attr="disabled">
                            {{ __('Link Aanmaken') }}
                        </x-button>
                    </x-slot>
                </x-form-section>

                <x-dialog-modal wire:model.live="displayingLink">
                    <x-slot name="title">
                        {{ __('Deur Link') }}
                    </x-slot>

                    <x-slot name="content">
                        <div>
                            {{ __('Kopieer a.u.b. de link om te delen.') }}
                        </div>

                        <x-input x-ref="link" type="text" readonly :value="$link"
                            class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 w-full break-all"
                            autofocus autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            @showing-token-modal.window="setTimeout(() => $refs.link.select(), 250)"
                        />
                    </x-slot>

                    <x-slot name="footer">
                        <x-secondary-button wire:click="$set('displayingLink', false)" wire:loading.attr="disabled">
                            {{ __('Close') }}
                        </x-secondary-button>
                    </x-slot>
                </x-dialog-modal>
            </div>
            HTML;
    }
}
