<?php

namespace Firebird\Schema;

use Firebird\FirebirdConnection;
use Illuminate\Database\Schema\Blueprint;

/**
 * @property FirebirdConnection $connection
 */
trait FirebirdAutoIncrement
{
    /**
     * create sequence and trigger for autoIncrement support.
     *
     * @param Blueprint $blueprint
     * @param string    $table
     *
     * @return void
     */
    public function createAutoIncrementObjects(Blueprint $blueprint, $table)
    {
        $column = $this->getQualifiedAutoIncrementColumn($blueprint);

        // return if no qualified AI column
        if (is_null($column)) {
            return;
        }

        $col = $column->name;
        $start = isset($column->start) ? $column->start : 1;

        // get table prefix
        $prefix = $this->connection->getTablePrefix();

        // create sequence for auto increment
        $sequenceName = $this->createObjectName($prefix, $table, $col, 'sq');
        $this->connection->getGenerator()->create($sequenceName);

        // create trigger for auto increment work around
        $triggerName = $this->createObjectName($prefix, $table, $col, 'tr');
        $this->connection->getTrigger()->autoIncrement($prefix . $table, $col, $triggerName, $sequenceName);
    }

    /**
     * Drop sequence and triggers if exists, autoincrement objects.
     *
     * @param  string $table
     * @return void
     */
    public function dropAutoIncrementObjects($table)
    {
        // drop sequence and trigger object
        $prefix = $this->connection->getTablePrefix();
        // get the actual primary column name from table
        $col = $this->getPrimaryKey($prefix . $table);
        // dump(array_merge(['method' => __METHOD__], compact('col', 'prefix')));

        // if primary key col is set, drop auto increment objects
        if (isset($col) && ! empty($col)) {
            // drop trigger for auto increment work around
            $triggerName = $this->createObjectName($prefix, $table, $col, 'tr');
            $drop_trigger = $this->connection->getTrigger()->drop($triggerName);
            // dump(compact('triggerName', 'drop_trigger'));

            // drop sequence for auto increment
            $sequenceName = $this->createObjectName($prefix, $table, $col, 'sq');
            $drop_seq = $this->connection->getGenerator()->drop($sequenceName);
            // dump(compact('sequenceName', 'drop_seq'));
        }
    }

    /**
     * Get qualified autoincrement column.
     *
     * @param  Blueprint $blueprint
     * @return \Illuminate\Support\Fluent|void
     */
    public function getQualifiedAutoIncrementColumn(Blueprint $blueprint)
    {
        $columns = $blueprint->getColumns();

        // search for primary key / autoIncrement column
        foreach ($columns as $column) {
            // if column is autoIncrement set the primary col name
            if ($column->autoIncrement) {
                return $column;
            }
        }
    }

    /**
     * Get table's primary key.
     *
     * @param  string $table
     * @return string
     */
    public function getPrimaryKey($table)
    {
        if (! $table) {
            return '';
        }

        $sql = "SELECT
                    TRIM(SG.RDB\$FIELD_NAME) AS \"column_name\"
                FROM
                    RDB\$INDICES IX
                    LEFT JOIN RDB\$INDEX_SEGMENTS SG ON IX.RDB\$INDEX_NAME = SG.RDB\$INDEX_NAME
                    LEFT JOIN RDB\$RELATION_CONSTRAINTS RC ON RC.RDB\$INDEX_NAME = IX.RDB\$INDEX_NAME
                WHERE
                    RC.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY'
                    AND UPPER(RC.RDB\$RELATION_NAME) = '" . strtoupper($table) . "'
                ORDER BY SG.RDB\$FIELD_POSITION";
        $data = $this->connection->selectOne($sql);

        if ($data) {
            return $data->column_name;
        }

        return '';
    }

    /**
     * Create an object name that limits to 30 chars.
     *
     * @param  string $prefix
     * @param  string $table
     * @param  string $col
     * @param  string $type
     * @return string
     */
    private function createObjectName($prefix, $table, $col, $type)
    {
        // keeps case compatibility with the $col string.
        $type = (ctype_upper($col) ? strtoupper($type) : $type);

        // remove "tb_" if exists
        $table = preg_replace('/^tb_/i', '', $table);

        $objectName = strtoupper($type . '_' . $prefix . $table . '_' . $col);

        // max object name length is 30 chars
        return substr($objectName, 0, 30);
    }
}