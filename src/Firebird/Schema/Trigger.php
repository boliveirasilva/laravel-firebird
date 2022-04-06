<?php

namespace Firebird\Schema;

use Illuminate\Database\Connection;

class Trigger
{
    use ReservedWords;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Function to create auto increment trigger for a table.
     *
     * @param  string $table
     * @param  string $column
     * @param  string $triggerName
     * @param  string $sequenceName
     * @return bool
     */
    public function autoIncrement($table, $column, $triggerName, $sequenceName)
    {
        if (! $table || ! $column || ! $triggerName || ! $sequenceName) {
            return false;
        }

        if ($this->connection->getConfig('prefix_schema')) {
            $table        = $this->connection->getConfig('prefix_schema') . '.' . $table;
            $triggerName  = $this->connection->getConfig('prefix_schema') . '.' . $triggerName;
            $sequenceName = $this->connection->getConfig('prefix_schema') . '.' . $sequenceName;
        }

        $table  = $this->wrapValue($table);
        $column = $this->wrapValue($column);

        $sql = "
            create trigger {$triggerName}
                active before insert 
                on {$table}
            as
            begin
                if (new.{$column} is null)
                then new.{$column} = next value for {$sequenceName};
            end
        ";

        // dump(['method' => __METHOD__, 'sql' => $sql]);
        return $this->connection->statement($sql);
    }

    /**
     * Wrap value if reserved word.
     *
     * @param string $value
     * @return string
     */
    protected function wrapValue($value)
    {
        return $this->isReserved($value) ? '"' . $value . '"' : $value;
    }

    /**
     * Function to safely drop trigger db object.
     *
     * @param  string $name
     * @return bool
     */
    public function drop($name)
    {
        if (! $name || ! $this->exists($name)) {
            return false;
        }

        $sql = 'drop trigger ' . $name;

        // dump(['method' => __METHOD__, 'sql' => $sql]);
        return $this->connection->statement($sql);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists($name)
    {
        if (! $name) {
            return false;
        }

        $sql = "SELECT RDB\$TRIGGER_NAME FROM RDB\$TRIGGERS WHERE UPPER(RDB\$TRIGGER_NAME) = '" . strtoupper($name) . "'";

        // dump(['method' => __METHOD__, 'sql' => $sql]);
        return (bool) $this->connection->selectOne($sql);
    }
}