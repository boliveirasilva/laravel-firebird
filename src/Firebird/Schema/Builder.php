<?php
namespace Firebird\Schema;

use Illuminate\Database\Schema\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    use FirebirdAutoIncrement;

    /**
     * Create a new table on the schema.
     *
     * @param  string $table
     * @param  \Closure $callback
     * @return void
     */
    public function create($table, \Closure $callback)
    {
        // $table = strtoupper($table);
        $blueprint = $this->createBlueprint($table);

        $blueprint->create();

        $callback($blueprint);

        $this->build($blueprint);

        $this->createAutoIncrementObjects($blueprint, $table);
    }

    public function drop($table)
    {
        $this->dropAutoIncrementObjects($table);
        parent::drop($table);
    }

    public function dropIfExists($table)
    {
        $this->dropAutoIncrementObjects($table);
        parent::dropIfExists($table);
    }

    // public function getColumnListing($table)
    // {
    //     $table = $this->connection->getTablePrefix().$table;
    //
    //     $results = $this->connection->select($this->grammar->compileColumnListing($table));
    //     // dd(['method' => __METHOD__, 'table' => $table, 'results' => $results]);
    //
    //     return $this->connection->getPostProcessor()->processColumnListing($results);
    // }

    // /**
    //  * Determine if the given table has a given column.
    //  *
    //  * @param  string  $table
    //  * @param  string  $column
    //  * @return bool
    //  */
    // public function hasColumn($table, $column)
    // {
    //     // dd(['method' => __METHOD__, /*, array_map('strtolower', $this->getColumnListing($table)),*/ $this->getColumnListing($table)]);
    //     return in_array(
    //         strtolower($column), array_map('strtolower', $this->getColumnListing($table))
    //     );
    // }
}