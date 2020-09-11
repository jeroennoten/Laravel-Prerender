<?php namespace Nutsweb\LaravelPrerender;

use Illuminate\Support\ServiceProvider;

class LaravelPrerenderServiceProvider extends ServiceProvider
{
    protected $package = 'nutsweb/laravel-prerender';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/prerender.php' => config_path('prerender.php')
        ], 'config');

        if ($this->app['config']->get('prerender.enable')) {
            /** @var \Illuminate\Foundation\Http\Kernel $kernel */
            $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
            $kernel->pushMiddleware(PrerenderMiddleware::class);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/prerender.php', 'prerender');
    }

}
