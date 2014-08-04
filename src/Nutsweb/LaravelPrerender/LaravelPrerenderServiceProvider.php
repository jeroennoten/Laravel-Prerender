<?php namespace Nutsweb\LaravelPrerender;

use App;
use Config;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class LaravelPrerenderServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $package = 'nutsweb/laravel-prerender';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package($this->package);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $namespace = $this->getPackageNamespace($this->package, null);
        $config = $this->guessPackagePath() . '/config';
        $this->app['config']->package($this->package, $config, $namespace);

        $config = $this->app['config']->get('laravel-prerender::config');

        if ($config['enable']) {

            $client = new Client(['base_url' => $config['prerender_url']]);
            $prerenderToken = $config['prerender_token'];
            $crawlerUserAgents = $config['crawler_user_agents'];
            $whitelist = $config['whitelist'];
            $blacklist = $config['blacklist'];

            $parameters = [$client, $prerenderToken, $crawlerUserAgents, $whitelist, $blacklist];
            App::middleware('Nutsweb\LaravelPrerender\PrerenderMiddleware', $parameters);

        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
