<?php

namespace Firebird\Query\Processors;

use Illuminate\Database\Query\Processors\Processor;

class FirebirdProcessor extends Processor
{
    /**
     * Process the results of a column listing query.
     *
     * @param array $results
     *
     * @return array
     */
    public function processColumnListing($results)
    {
        return array_map(function ($result) {
            $obj = (object)$result;
            return $obj->column_name;
        }, $results);
    }
}