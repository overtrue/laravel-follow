<?php
namespace Overtrue\LaravelFollow\Test;

use Illuminate\Filesystem\ClassFinder;
use Illuminate\Filesystem\Filesystem;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->migrate();
        $this->seed();
    }

    /**
     * run package database migrations
     *
     * @return void
     */
    public function migrate()
    {
        $fileSystem = new Filesystem();
        $classFinder = new ClassFinder();

        foreach ($fileSystem->files(__DIR__ . "/../tests/database/migrations") as $file) {
            $fileSystem->requireOnce($file);
            $migrationClass = $classFinder->findClass($file);

            (new $migrationClass)->up();
        }
    }

    /**
     * Seed testing database.
     */
    public function seed($classname = null)
    {
        User::create([ 'name' => 'John' ]);
        User::create([ 'name' => 'Allison' ]);
        User::create([ 'name' => 'Ron' ]);

        Other::create([ 'name' => 'Laravel' ]);
        Other::create([ 'name' => 'Vuejs' ]);
        Other::create([ 'name' => 'Ruby' ]);
    }
}