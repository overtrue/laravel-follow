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
        $root = dirname(__DIR__);

        if (!file_exists(config_path('follow.php'))) {
            $this->publishes([
                $root.'/config/follow.php' => config_path('follow.php'),
            ], 'config');
        }

        if (!class_exists('CreateLaravelFollowTables')) {
            $datePrefix = date('Y_m_d_His');
            $this->publishes([
                $root.'/database/migrations/create_laravel_follow_tables.php' => database_path("/migrations/{$datePrefix}_create_laravel_follow_tables.php"),
            ], 'migrations');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/follow.php', 'follow');
    }
}
