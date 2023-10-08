<?php

namespace Tests\Feature\Livewire;

use App\Livewire\LockPanel;
use Livewire\Livewire;
use Tests\TestCase;

class LockPanelTest extends TestCase
{
    /**
     * @test
     */
    public function guest_cannot_render_page()
    {
        Livewire::test(LockPanel::class)
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function user_can_render_page()
    {
        Livewire::test(LockPanel::class)
            ->assertOk();
    }

    /**
     * @test
     */
    public function user_can_activate_lock()
    {
        //
    }
}
