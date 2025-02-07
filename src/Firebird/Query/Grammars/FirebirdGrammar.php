<?php

namespace Firebird\Query\Grammars;

use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Builder;

class FirebirdGrammar extends Grammar
{

    /**
     * The components that make up a select clause.
     *
     * @var array
     */
    protected $selectComponents = array(
        'aggregate',
        'limit',
        'offset',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders'
    );

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $table
     * @return string
     */
    public function wrapTable($table)
    {
        $tableName = (
            !$this->isExpression($table)
            ? $this->wrap($this->tablePrefix.$table, true)
            : $this->getValue($table)
        );

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
        $data = null;

        if ($this->isExpression($value)) {
            $data = parent::getValue($value);

        } elseif (strpos(strtolower($value), ' as ') !== false) {
            // If the value being wrapped has a column alias we will need to separate out
            // the pieces so we can wrap each of the segments of the expression on it
            // own, and then joins them both back together with the "as" connector.
            return $this->wrapAliasedValue($value, $prefixAlias);

        } else {
            $data = $this->wrapSegments(explode('.', $value));
        }

        return strtoupper(str_replace('"', '', $data));
    }


    /**
     * Compile a select query into SQL.
     *
     * @param Illuminate\Database\Query\Builder
     *
     * @return string
     */
    public function compileSelect(Builder $query)
    {
        if (is_null($query->columns)) {
            $query->columns = array('*');
        }

        return trim($this->concatenate($this->compileComponents($query)));
    }

    /**
     * Compile the "select *" portion of the query.
     * As Firebird adds the "limit" and "offset" after the "select", this must not work this way.
     *
     * @param Builder $query
     * @param array   $columns
     *
     * @return string
     */
    protected function compileColumns(Builder $query, $columns)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (!is_null($query->aggregate)) {
            return;
        }
        $select = '';
        if (count($columns) > 0 && $query->limit == null && $query->aggregate == null) {
            $select = $query->distinct ? 'select distinct ' : 'select ';
        }

        return $select . $this->columnize($columns);
    }

    /**
     * Compile an aggregated select clause.
     *
     * @param Builder $query
     * @param array   $aggregate
     *
     * @return string
     */
    protected function compileAggregate(Builder $query, $aggregate)
    {
        $column = $this->columnize($aggregate['columns']);

        // If the query has a "distinct" constraint and we're not asking for all columns
        // we need to prepend "distinct" onto the column name so that the query takes
        // it into account when it performs the aggregating operations on the data.
        if ($query->distinct && $column !== '*') {
            $column = 'distinct ' . $column;
        }

        return 'select ' . $aggregate['function'] . '(' . $column . ') as aggregate';
    }

    /**
     * Compile first instead of limit
     *
     * @param Builder $query
     * @param int     $limit
     *
     * @return string
     */
    protected function compileLimit(Builder $query, $limit)
    {
        return 'select first ' . (int)$limit;
    }

    /**
     * Compile skip instead of offset
     *
     * @param Builder $query
     * @param int     $limit
     *
     * @return string
     */
    protected function compileOffset(Builder $query, $limit)
    {
        return 'skip ' . (int)$limit;
    }
}