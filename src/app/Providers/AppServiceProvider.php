<?php

namespace App\Providers;

use App\Services\BookingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BookingService::class);
    }

    public function boot(): void
    {
        //
    }
}
