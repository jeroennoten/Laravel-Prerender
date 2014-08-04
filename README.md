## Installation

Require this package in your composer.json and run composer update (or run `composer require nutsweb/laravel-prerender:dev-master` directly):

    "nutsweb/laravel-prerender": "dev-master"

After updating composer, add the ServiceProvider to the providers array in app/config/app.php.

    'Nutsweb/LaravelPrerender/LaravelPrerenderServiceProvider',

Publish the configuration file.

    $ php artisan config:publish nutsweb/laravel-prerender

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)