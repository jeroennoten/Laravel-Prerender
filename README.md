[![Build Status](https://travis-ci.org/JeroenNoten/Laravel-Prerender.svg?branch=master)](https://travis-ci.org/JeroenNoten/Laravel-Prerender)

Laravel middleware for prerendering javascript-rendered pages on the fly for SEO 

## Installation

Require this package in your composer.json and run composer update (or run `composer require nutsweb/laravel-prerender:dev-master` directly):

    "nutsweb/laravel-prerender": "dev-master"

After updating composer, add the ServiceProvider to the providers array in app/config/app.php.

    'Nutsweb/LaravelPrerender/LaravelPrerenderServiceProvider',

Publish the configuration file.

    $ php artisan config:publish nutsweb/laravel-prerender

If you want to make use of the prerender.io service, fill in your token in the configuration file.
    
    // config.php
    'prerender_token' => 'YOUR-TOKEN',
    
If you are using a self-hosted service, change the server address in the configuration file.

    // config.php
    'prerender_url' => 'http://example.com',

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### To Do
- Comments
- Tests
- Copy readme from other prerender framework integrations
