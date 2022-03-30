<?php

namespace Firebird\Schema;

use Illuminate\Database\Connection;

class Generator
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * function to create oracle sequence.
     *
     * @param  string $name
     * @return bool
     */
    public function create($name)
    {
        if (! $name) {
            return false;
        }

        if ($this->connection->getConfig('prefix_schema')) {
            $name = $this->connection->getConfig('prefix_schema') . '.' . $name;
        }

        // dump([__METHOD__, 'SQL>> create sequence "' . $name . '"']);
        return $this->connection->statement('create sequence "' . $name . '"');
    }

    /**
     * function to safely drop sequence db object.
     *
     * @param  string $name
     * @return bool
     */
    public function drop($name)
    {
        // check if a valid name and sequence exists
        if (! $name || ! $this->exists($name)) {
            return false;
        }

        // dump([__METHOD__, 'SQL>> drop sequence "' . $name . '"']);
        return $this->connection->statement('drop sequence "' . $name . '"');
    }

    /**
     * function to check if sequence exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        if (! $name) {
            return false;
        }

        // dump([__METHOD__, "SQL>>
        //     SELECT RDB\$GENERATOR_NAME FROM RDB\$GENERATORS
        //     WHERE RDB\$SYSTEM_FLAG IS DISTINCT FROM 1 AND UPPER(RDB\$GENERATOR_NAME) = UPPER('{$name}')
        // "]);
        return (bool) $this->connection->selectOne("
            SELECT RDB\$GENERATOR_NAME FROM RDB\$GENERATORS 
            WHERE RDB\$SYSTEM_FLAG IS DISTINCT FROM 1 AND UPPER(RDB\$GENERATOR_NAME) = UPPER('{$name}')
        ");
    }

    /**
     * get sequence next value.
     *
     * @param  string $name
     * @return int
     */
    public function nextValue($name)
    {
        if (! $name) {
            return 0;
        }

        // dump([__METHOD__, 'SQL>> SELECT NEXT VALUE FOR "'.$name.'" AS id FROM RDB$DATABASE']);
        return $this->connection->selectOne('SELECT NEXT VALUE FOR "'.$name.'" AS id FROM RDB$DATABASE')->id;
    }

    /**
     * same function as lastInsertId. added for clarity with oracle sql statement.
     *
     * @param  string $name
     * @return int
     */
    public function currentValue($name)
    {
        return $this->lastInsertId($name);
    }

    /**
     * function to get oracle sequence last inserted id.
     *
     * @param  string $name
     * @return int
     */
    public function lastInsertId($name)
    {
        // check if a valid name and sequence exists
        if (! $name || ! $this->exists($name)) {
            return 0;
        }

        // dump([__METHOD__, 'SQL>> SELECT GEN_ID("'.$name.'", 0) AS id FROM RDB$DATABASE']);
        return $this->connection->selectOne('SELECT GEN_ID("'.$name.'", 0) AS id FROM RDB$DATABASE')->id;
    }
}