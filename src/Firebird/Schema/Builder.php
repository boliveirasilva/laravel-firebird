<?php
namespace Firebird\Schema;

use Illuminate\Database\Schema\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    // public function drop($table)
    // {
    //     dump(['method' => __METHOD__, 'table' => $table]);
    //     parent::drop($table);
    // }

    // public function dropIfExists($table)
    // {
    //     dump(['method' => __METHOD__, 'table' => $table]);
    //     parent::dropIfExists($table);
    // }

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