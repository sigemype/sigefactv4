<?php

namespace Modules\Dispatch\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Dispatch\Models\Dispatcher;
use Modules\Dispatch\Models\DispatchItem;
use Modules\Dispatch\Models\Driver;
use Modules\Dispatch\Models\Transport;
use Modules\Dispatch\Observers\DispatcherObserver;
use Modules\Dispatch\Observers\DriverObserver;
use Modules\Dispatch\Observers\TransportObserver;

class DispatchServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerViews();
        Driver::observe(DriverObserver::class);
        Dispatcher::observe(DispatcherObserver::class);
        Transport::observe(TransportObserver::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/dispatch');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/dispatch';
        }, \Config::get('view.paths')), [$sourcePath]), 'dispatch');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
