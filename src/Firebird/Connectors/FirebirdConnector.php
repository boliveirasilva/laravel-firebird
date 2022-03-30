<?php

namespace Firebird\Connectors;

use PDO;
use InvalidArgumentException;
use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;

class FirebirdConnector extends Connector implements ConnectorInterface
{
    /**
     * Establish a database connection.
     *
     * @param array $config
     *
     * @return PDO
     * @throws InvalidArgumentException
     */
    public function connect(array $config)
    {
        $options = $this->getOptions($config);

        $dsn = $this->getDsn($config);
        return $this->createConnection($dsn, $config, $options);
    }

    /**
     * Return the DSN string from configuration
     *
     * @param array $config
     *
     * @return string
     */
    protected function getDsn(array $config)
    {
        // Check that the host and database are not empty
        if (empty($config['host']) || empty($config['database'])) {
            throw new InvalidArgumentException('Cannot connect to Firebird Database, no host or path supplied');
        }

        $path = $config['database'];
        $host = $config['host'] . (!empty($config['port']) ? '/' . $config['port'] : null);
        $charset = (!empty($config['charset']) ? ';charset=' . $config['charset'] : null);

        return sprintf('firebird:dbname=%s:%s%s', $host, $path, $charset);
    }
}