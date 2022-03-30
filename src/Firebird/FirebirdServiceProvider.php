<?php
namespace Firebird;

use Illuminate\Support\ServiceProvider;
use Firebird\Connectors\FirebirdConnector;
use Illuminate\Database\Connection;

class FirebirdServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the application services.
     * This is where the connection gets registered
     *
     * @return void
     */
    public function register()
    {
        Connection::resolverFor('firebird', function ($connection, $database, $prefix, $config) {
            $connector = new FirebirdConnector();
            $connection = $connector->connect($config);
            return new FirebirdConnection($connection, $database, $prefix, $config);
        });
    }
}
