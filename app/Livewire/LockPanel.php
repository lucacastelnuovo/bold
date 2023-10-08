<?php

namespace App\Livewire;

use App\Enums\Lock;
use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Component;

class LockPanel extends Component
{
    #[Locked]
    public ?User $owner;

    #[Locked]
    public string $causer = '';

    public function mount(): void
    {
        $this->owner = User::findOrFail(request()->query('owner'));
        $this->causer = request()->query('causer');
    }

    public function voordeur(): void
    {
        $lock = Lock::VOORDEUR;
        $message = $lock->trigger($this->owner, $this->causer);

        $alert = __(':Lock - :Message', [
            'Lock'    => $lock->value,
            'Message' => $message,
        ]);

        $this->js("alert('{$alert}')");
    }

    public function bovendeur(): void
    {
        $lock = Lock::BOVENDEUR;
        $message = $lock->trigger($this->owner, $this->causer);

        $alert = __(':Lock - :Message', [
            'Lock'    => $lock->value,
            'Message' => $message,
        ]);

        $this->js("alert('{$alert}')");
    }

    public function render(): string
    {
        return <<<'HTML'
            <div class="h-screen max-w-xl mx-auto flex flex-col items-center justify-center gap-4">
                <x-button type="button" class="px-6 py-8" wire:click="voordeur" wire:loading.attr="disabled">
                    {{ App\Enums\Lock::VOORDEUR->value }}
                </x-button>

                <x-button type="button" class="px-6 py-8" wire:click="bovendeur" wire:loading.attr="disabled">
                    {{ App\Enums\Lock::BOVENDEUR->value }}
                </x-button>
            </div>
            HTML;
    }
}
