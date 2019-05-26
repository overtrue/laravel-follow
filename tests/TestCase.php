<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow\Test;

use Illuminate\Filesystem\Filesystem;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    protected $config;

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        return $app;
    }

    /**
     * Setup DB before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (empty($this->config)) {
            $this->config = require __DIR__.'/../config/follow.php';
        }

        $this->app['config']->set('follow', $this->config);
        $this->app['config']->set('follow.user_model', User::class);

        $this->migrate();
        $this->seed();
    }

    /**
     * run package database migrations.
     */
    public function migrate()
    {
        $fileSystem = new Filesystem();

        $fileSystem->copy(
            __DIR__.'/../database/migrations/2018_06_29_032244_create_laravel_follow_tables.php',
            __DIR__.'/database/migrations/create_laravel_follow_tables.php'
        );

        foreach ($fileSystem->files(__DIR__.'/database/migrations') as $file) {
            $fileSystem->requireOnce($file);
        }

        (new \CreateLaravelFollowTables())->up();
        (new \CreateUsersTable())->up();
        (new \CreateOthersTable())->up();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unlink(__DIR__.'/database/migrations/create_laravel_follow_tables.php');
    }

    /**
     * Seed testing database.
     */
    public function seed($classname = null)
    {
        User::create(['name' => 'John']);
        User::create(['name' => 'Allison']);
        User::create(['name' => 'Ron']);

        Other::create(['name' => 'Laravel']);
        Other::create(['name' => 'Vuejs']);
        Other::create(['name' => 'Ruby']);
    }
}
