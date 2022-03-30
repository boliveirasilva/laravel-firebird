<?php

namespace Firebird;

use Firebird\Schema\Trigger;
use Firebird\Schema\Generator;
use Illuminate\Database\Grammar;
use Illuminate\Database\Connection;
use Firebird\Query\Processors\FirebirdProcessor;
use Firebird\Schema\Grammars\FirebirdGrammar as FirebirdSchemaGrammar;
use Firebird\Query\Grammars\FirebirdGrammar as FirebirdQueryGrammar;
use Firebird\Schema\Builder as FirebirdSchemaBuilder;
use Firebird\Query\Builder as FirebirdQueryBuilder;

class FirebirdConnection extends Connection
{
    /** @var Generator */
    protected $generator;

    /** @var Trigger */
    protected $trigger;


    /**
     * Create a new database connection instance.
     *
     * @param        $pdo
     * @param string $database
     * @param string $tablePrefix
     * @param array  $config
     */
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = array())
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
        $this->generator = new Generator($this);
        $this->trigger = new Trigger($this);
    }

    /**
     * Get the default query grammar instance
     *
     * @return FirebirdQueryGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return new FirebirdQueryGrammar;
    }

    /**
     * Get the default post processor instance.
     *
     * @return FirebirdProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new FirebirdProcessor;
    }

    /**
     * Get a schema builder instance for this connection.
     * @return FirebirdSchemaBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new FirebirdSchemaBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return Grammar;
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new FirebirdSchemaGrammar);
    }

    /**
     * Get a new query builder instance.
     *
     * @return FirebirdQueryBuilder
     */
    public function query()
    {
        return new FirebirdQueryBuilder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }

    /**
     * Get sequence class.
     *
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Set sequence class.
     *
     * @param Generator $generator
     *
     * @return Generator
     */
    public function setGenerator(Generator $generator)
    {
        dump(__METHOD__);
        return $this->generator = $generator;
    }

    /**
     * Get oracle trigger class.
     *
     * @return Trigger
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * Set oracle trigger class.
     *
     * @param Trigger $trigger
     * @return Trigger
     */
    public function setTrigger(Trigger $trigger)
    {
        dump(__METHOD__);
        return $this->trigger = $trigger;
    }
}
