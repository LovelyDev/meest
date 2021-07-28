<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Extensions\CustomFileSessionHandler\CustomFileSessionHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      // We register our custom file session handler, so we can
      // define it in config/sesssion.php.
      Session::extend('custom_file', function ($app) {
        return new CustomFileSessionHandler;
      });
    }
}
