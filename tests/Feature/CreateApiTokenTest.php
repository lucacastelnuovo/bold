<?php

namespace Tests\Feature;

use App\Enums\Lock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

class CreateApiTokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function api_tokens_can_be_created(): void
    {
        if (!Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');

            return;
        }

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        Livewire::test(ApiTokenManager::class)
            ->set(['createApiTokenForm' => [
                'name'        => 'Test Token',
                'permissions' => [
                    Lock::VOORDEUR->value,
                ],
            ]])
            ->call('createApiToken');

        $this->assertCount(1, $user->fresh()->tokens);
        $this->assertEquals('Test Token', $user->fresh()->tokens->first()->name);
        $this->assertTrue($user->fresh()->tokens->first()->can(Lock::VOORDEUR->value));
        $this->assertFalse($user->fresh()->tokens->first()->can(Lock::BOVENDEUR->value));
    }
}
