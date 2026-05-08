<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use App\CustomHelperFacade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind 'CustomHelper' to the container
        $this->app->singleton('CustomHelper', function () {
            return new \App\Helpers\CustomHelper();
        });

        // Register alias manually for facade usage
        AliasLoader::getInstance()->alias('CustomHelper', \App\CustomHelperFacade::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
