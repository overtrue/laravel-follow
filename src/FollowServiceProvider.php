<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow;

use Illuminate\Support\ServiceProvider;

class FollowServiceProvider extends ServiceProvider
{
    /**
     * Application bootstrap event.
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__.'/../config/follow.php') => config_path('follow.php'),
        ], 'config');

        $this->publishes([
            realpath(__DIR__.'/../database/migrations') => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/follow.php'), 'follow');
    }
}
