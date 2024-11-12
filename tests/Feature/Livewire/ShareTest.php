<?php

use App\Livewire\Share;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Share::class)
        ->assertStatus(200);
});
