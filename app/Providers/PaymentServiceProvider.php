<?php

namespace App\Providers;

use App\Contracts\PaymentInterface;
use App\Http\Controllers\PaystackController;
use App\Services\PaystackService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(PaystackController::class)
            ->needs(PaymentInterface::class)
            ->give(PaystackService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
