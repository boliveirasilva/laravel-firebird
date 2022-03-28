<?php

namespace FirebirdTests;

use Firebird\FirebirdServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/Support/Factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            FirebirdServiceProvider::class
        ];
    }

    /**
     * Define database migrations.
     *
     * @return void
     * @throws \Exception
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'firebird');
        config()->set('database.connections.firebird', [
            'driver' => 'firebird',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3050'),
            'database' => env('DB_DATABASE', '/firebird/data/database.fdb'),
            'username' => env('DB_USERNAME', 'sysdba'),
            'password' => env('DB_PASSWORD', 'masterkey'),
            'charset' => env('DB_CHARSET', 'UTF8'),
        ]);
    }
}