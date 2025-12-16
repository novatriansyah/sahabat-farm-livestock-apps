<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Animal;
use App\Observers\AnimalObserver;

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
        Animal::observe(AnimalObserver::class);
    }
}
