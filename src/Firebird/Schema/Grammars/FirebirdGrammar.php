<?php

namespace Firebird\Schema\Grammars;

use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;
use Illuminate\Database\Schema\Blueprint;
use Firebird\FirebirdConnection;

class FirebirdGrammar extends Grammar
{

    /**
     * The possible column modifiers.
     *
     * @var array
     */
    protected $modifiers = ['Increment', 'Default', 'Nullable'];

    /**
     * The columns available as serials.
     *
     * @var array
     */
    protected $serials = ['integer'];

    /**
     * Compile the query to determine if a table exists.
     *
     * @return string
     */
    public function compileTableExists()
    {
        return "SELECT RDB\$RELATION_NAME FROM RDB\$RELATIONS WHERE UPPER(RDB\$RELATION_NAME) = UPPER(?)";
    }

    /**
     * Compile the query to determine the list of columns.
     *
     * @param string $table
     *
     * @return string
     */
    public function compileColumnListing($table)
    {
        return "SELECT TRIM(RDB\$FIELD_NAME) AS \"column_name\" FROM RDB\$RELATION_FIELDS WHERE RDB\$RELATION_NAME = '$table'";

        // dump(['method' => __METHOD__, 'sql' => $sql]);
        // return $sql;
    }

    /**
     * Compile a create table command.
     *
     * @param Blueprint                       $blueprint
     * @param Fluent                          $command
     * @param \Illuminate\Database\Connection $connection
     *
     * @return string
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command, FirebirdConnection $connection)
    {
        $columns = implode(', ', $this->getColumns($blueprint));

        return 'create table ' . $this->wrapTable($blueprint) . " ($columns)";

        // dump(['method' => __METHOD__, 'sql' => $sql]);
        // return $sql;
    }

    /**
     * Compile a drop table (if exists) command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compileDropIfExists(Blueprint $blueprint, Fluent $command)
    {
        return sprintf(
            "execute block as begin if (exists(%s)) then execute statement '%s'; end",
            str_replace('?', "'".$this->wrapTable($blueprint)."'", $this->compileTableExists()),
            $this->compileDrop($blueprint, $command)
        );

        // dump(['method' => __METHOD__, 'sql' => $sql]);
        // return $sql;
    }

    /**
     * Compile a drop table command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compileDrop(Blueprint $blueprint, Fluent $command)
    {
        return 'drop table ' . $this->wrapTable($blueprint);
        // dump(['method' => __METHOD__, 'sql' => $sql]);
        // return $sql;
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param  mixed   $table
     * @return string
     */
    public function wrapTable($table)
    {
        $tableName = parent::wrapTable(
            $table instanceof Blueprint ? $table->getTable() : $table
        );

        // dump(['method' => __METHOD__, 'table' => strtoupper(str_replace('"', '', $tableName))]);
        return strtoupper(str_replace('"', '', $tableName));
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $value
     * @param  bool    $prefixAlias
     * @return string
     */
    public function wrap($value, $prefixAlias = false)
    {
        $data = parent::wrap(
            $value instanceof Fluent ? $value->name : $value, $prefixAlias
        );

        return str_replace('"', '', $data);
    }

    /**
     * Compile a primary key command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compilePrimary(Blueprint $blueprint, Fluent $command)
    {
        $command->name(null);

        return $this->compileKey($blueprint, $command, 'primary key');
        // dump(['method' => __METHOD__, 'sql' => $sql]);
    }

    /**
     * Compile an index creation command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     * @param string    $type
     *
     * @return string
     */
    protected function compileKey(Blueprint $blueprint, Fluent $command, $type)
    {
        $columns = $this->columnize($command->columns);

        $table = $this->wrapTable($blueprint);

        return "alter table {$table} add {$type} ($columns)";
        // dump(['method' => __METHOD__, 'sql' => $sql]);
    }

    /**
     * Compile a unique key command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compileUnique(Blueprint $blueprint, Fluent $command)
    {
        $columns = $this->columnize($command->columns);

        $table = $this->wrapTable($blueprint);

        return "CREATE UNIQUE INDEX " . strtoupper(substr($command->index, 0, 31)) . " ON {$table} ($columns)";
        // dump(['method' => __METHOD__, 'sql' => $sql]);
    }

    /**
     * Compile a plain index key command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compileIndex(Blueprint $blueprint, Fluent $command)
    {
        $columns = $this->columnize($command->columns);

        $table = $this->wrapTable($blueprint);

        return "CREATE INDEX " . strtoupper(substr($command->index, 0, 31)) . " ON {$table} ($columns)";
        // dump(['method' => __METHOD__, 'sql' => $sql]);
    }

    /**
     * Compile a foreign key command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compileForeign(Blueprint $blueprint, Fluent $command)
    {
        $table = $this->wrapTable($blueprint);

        $on = $this->wrapTable($command->on);

        // We need to prepare several of the elements of the foreign key definition
        // before we can create the SQL, such as wrapping the tables and convert
        // an array of columns to comma-delimited strings for the SQL queries.
        $columns = $this->columnize($command->columns);

        $onColumns = $this->columnize((array)$command->references);

        $sql = "alter table {$table} add constraint " . strtoupper(substr($command->index, 0, 31)) . " ";

        $sql .= "foreign key ({$columns}) references {$on} ({$onColumns})";

        // Once we have the basic foreign key creation statement constructed we can
        // build out the syntax for what should happen on an update or delete of
        // the affected columns, which will get something like "cascade", etc.
        if (!is_null($command->onDelete)) {
            $sql .= " on delete {$command->onDelete}";
        }

        if (!is_null($command->onUpdate)) {
            $sql .= " on update {$command->onUpdate}";
        }

        // dump(['method' => __METHOD__, 'sql' => $sql]);
        return $sql;
    }

    /**
     * Compile a drop foreign key command.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compileDropForeign(Blueprint $blueprint, Fluent $command)
    {
        $table = $this->wrapTable($blueprint);

        return "alter table {$table} drop constraint {$command->index}";
        // dump(['method' => __METHOD__, 'sql' => $sql]);
    }

    /**
     * Get the SQL for a nullable column modifier.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $column
     *
     * @return string|null
     */
    protected function modifyNullable(Blueprint $blueprint, Fluent $column)
    {
        return $column->nullable ? '' : ' not null';
    }

    /**
     * Get the SQL for a default column modifier.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $column
     *
     * @return string|void
     */
    protected function modifyDefault(Blueprint $blueprint, Fluent $column)
    {
        if (!is_null($column->default)) {
            return " default " . $this->getDefaultValue($column->default);
        }
    }

    /**
     * Get the SQL for an auto-increment column modifier.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $column
     *
     * @return string|void
     */
    protected function modifyIncrement(Blueprint $blueprint, Fluent $column)
    {
        if (in_array($column->type, $this->serials) && $column->autoIncrement) {
            return ' primary key';
        }
    }

    /**
     * Create the column definition for a char type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeChar(Fluent $column)
    {
        return 'VARCHAR';
    }

    /**
     * Create the column definition for a string type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeString(Fluent $column)
    {
        return 'VARCHAR (' . $column->length . ')';
    }

    /**
     * Create the column definition for a text type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeText(Fluent $column)
    {
        return 'BLOB SUB_TYPE TEXT';
    }

    /**
     * Create the column definition for a medium text type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeMediumText(Fluent $column)
    {
        return 'BLOB SUB_TYPE TEXT';
    }

    /**
     * Create the column definition for a long text type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeLongText(Fluent $column)
    {
        return 'BLOB SUB_TYPE TEXT';
    }

    /**
     * Create the column definition for a integer type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeInteger(Fluent $column)
    {
        return 'INTEGER';
    }

    /**
     * Create the column definition for a big integer type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeBigInteger(Fluent $column)
    {
        return 'INTEGER';
    }

    /**
     * Create the column definition for a medium integer type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeMediumInteger(Fluent $column)
    {
        return 'INTEGER';
    }

    /**
     * Create the column definition for a tiny integer type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeTinyInteger(Fluent $column)
    {
        return 'SMALLINT';
    }

    /**
     * Create the column definition for a small integer type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeSmallInteger(Fluent $column)
    {
        return 'SMALLINT';
    }

    /**
     * Create the column definition for a float type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeFloat(Fluent $column)
    {
        return 'FLOAT';
    }

    /**
     * Create the column definition for a double type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeDouble(Fluent $column)
    {
        return 'DOUBLE';
    }

    /**
     * Create the column definition for a decimal type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeDecimal(Fluent $column)
    {
        return 'DECIMAL';
    }

    /**
     * Create the column definition for a boolean type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeBoolean(Fluent $column)
    {
        return 'CHAR(1)';
    }

    /**
     * Create the column definition for an enum type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeEnum(Fluent $column)
    {
        return 'VARCHAR';
    }

    /**
     * Create the column definition for a json type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeJson(Fluent $column)
    {
        return 'BLOB SUB_TYPE 0';
    }

    /**
     * Create the column definition for a date type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeDate(Fluent $column)
    {
        return 'TIMESTAMP';
    }

    /**
     * Create the column definition for a date-time type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeDateTime(Fluent $column)
    {
        return 'TIMESTAMP';
    }

    /**
     * Create the column definition for a time type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeTime(Fluent $column)
    {
        return 'TIMESTAMP';
    }

    /**
     * Create the column definition for a timestamp type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeTimestamp(Fluent $column)
    {
        return 'TIMESTAMP';
    }

    /**
     * Create the column definition for a binary type.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeBinary(Fluent $column)
    {
        return 'BLOB SUB_TYPE 0';
    }
}
