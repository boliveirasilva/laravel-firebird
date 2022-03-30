<?php
namespace Firebird;

use Illuminate\Database\DatabaseManager as BaseDatabaseManager;
use Firebird\_ConnectionFactory as FirebirdConnectionFactory;
use Illuminate\Foundation\Application;

class _DatabaseManager extends BaseDatabaseManager
{
    /**
     * Create a new database manager instance.
     *
     * @param Application                $app
     * @param \Illuminate\Database\Connectors\ConnectionFactory $factory
     *
     * @return void
     */
    public function __construct($app, FirebirdConnectionFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }
}