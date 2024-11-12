<?php

namespace App\Providers;

use App\Services\Bold\BoldService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BoldService::class, function () {
            $client = Http::baseUrl('https://api.boldsmartlock.com')
                ->connectTimeout(3)
                ->timeout(10)
                ->acceptJson()
                ->withHeader('Bold-Client-Token', config('services.bold.firewall_key'))
                ->withUserAgent(config('app.domain'))
                ->throw();

            return new BoldService($client);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* Performance restrictions are not applied in production */
        Model::preventLazyLoading(!app()->isProduction());

        /* Destructive restrictions are applied in production and staging */
        DB::prohibitDestructiveCommands(app()->environment(['production', 'staging']));

        /* Correctness restrictions are always applied */
        Model::preventAccessingMissingAttributes();
        Model::preventSilentlyDiscardingAttributes();

        /* Define morph aliasses */
        Relation::enforceMorphMap([
            'user' => \App\Models\User::class,
            'lock' => \App\Models\Lock::class,
        ]);
    }
}
