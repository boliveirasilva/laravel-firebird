<?php

namespace Firebird;

use PDO;
use InvalidArgumentException;
use Firebird\_FirebirdConnector;
use Firebird\_Connection as FirebirdConnection;
use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\ConnectionFactory as BaseConnectionFactory;

class _ConnectionFactory extends BaseConnectionFactory
{
    /**
     * Create a connector instance based on the configuration.
     *
     * @param array $config
     *
     * @return ConnectorInterface
     *
     * @throws InvalidArgumentException
     */
    public function createConnector(array $config)
    {
        if (isset($config['driver']) && $config['driver'] == 'firebird') {
            return new _FirebirdConnector;
        }

        return parent::createConnector($config);
    }

    /**
     * Create a new connection instance.
     *
     * @param string $driver
     * @param PDO    $connection
     * @param string $database
     * @param string $prefix
     * @param array  $config
     *
     * @return \Illuminate\Database\Connection
     *
     * @throws InvalidArgumentException
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = array())
    {
        if (!$this->container->bound("db.connection.{$driver}") && $driver == 'firebird') {
            return new FirebirdConnection($connection, $database, $prefix, $config);
        }

        return parent::createConnection($driver, $connection, $database, $prefix, $config);
    }
}
