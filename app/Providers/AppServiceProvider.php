<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        /* Performance restrictions are not applied in production */
        Model::preventLazyLoading(!app()->isProduction());

        /* Correctness restrictions are always applied */
        Model::preventAccessingMissingAttributes();
        Model::preventSilentlyDiscardingAttributes();
    }
}
