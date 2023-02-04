<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['request']->server->set('HTTPS', 'true');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (glob(__DIR__.'/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
        Paginator::useBootstrap();

    }
}
