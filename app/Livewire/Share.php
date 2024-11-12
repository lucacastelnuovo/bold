<?php

namespace App\Livewire;

use App\Models\Lock;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class Share extends Component
{
    #[Url(as: 'user'), Locked]
    public int $url_user;

    #[Url(as: 'guest'), Locked]
    public string $url_guest;

    #[Url(as: 'expires'), Locked]
    public int $url_expires;

    protected User $user;
    protected Collection $locks;
    protected string $guest;
    protected Carbon $expires;

    public function mount()
    {
        $this->user = User::findOrFail($this->url_user);
        $this->locks = $this->user->locks;
        $this->guest = base64_decode($this->url_guest, true);
        $this->expires = Carbon::parse($this->url_expires);
    }

    public function render()
    {
        return view('livewire.share');
    }

    public function activate(Lock $lock)
    {
        $lock->activate(guestName: $this->url_guest);
    }
}
