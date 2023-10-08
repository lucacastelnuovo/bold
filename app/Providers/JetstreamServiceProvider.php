<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Enums\Lock;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions([
            Lock::VOORDEUR->value,
            Lock::BOVENDEUR->value,
        ]);

        Jetstream::permissions(Lock::getValues());
    }
}
