<?php

namespace FirebirdTests;

use FirebirdTests\Support\Models\TestbenchUser;
use Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Schema\Builder as SchemaBuilder;

class AutoIncrementMigrationTest extends TestCase
{
    /** @var SchemaBuilder */
    private $schemaBuilder;

    private $tableName = 'testbench_users';

    private $tableCreated;


    protected function setUp()
    {
        parent::setUp();

        $this->schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();

        echo PHP_EOL;
        $this->migrateDatabase();
        $this->seedDatabase();
    }

    protected function tearDown()
    {
        if ($this->tableCreated) {
            echo '-> Removendo a tabela do banco...', PHP_EOL;
            $this->schemaBuilder->drop($this->tableName);
        }

        parent::tearDown();
    }

    protected function migrateDatabase()
    {
        echo '-> Garantindo que a tabela de teste não existe no banco.', PHP_EOL;
        $this->schemaBuilder->dropIfExists($this->tableName);

        echo '-> Criando a tabela de teste...', PHP_EOL;
        $this->schemaBuilder->create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });

        $this->tableCreated = $this->schemaBuilder->hasTable($this->tableName);
    }

    protected function seedDatabase()
    {
        echo '-> Alimentando dados na tabela de forma a utilizar o auto-incremento...', PHP_EOL;
        collect(range(1, 20))->each(function ($i) {
            TestbenchUser::query()->create([
                'name' => 'Record-' . $i,
                'email' => 'Email-' . $i . '@example.com',
            ]);
        });

        // $users = \DB::table($this->tableName)->get()->pluck('name', 'id');
        // dump($users);
    }

    /** @test */
    public function it_runs_the_migrations()
    {
        echo '-> Testando a criação/hidratação da tabela.', PHP_EOL;
        $users = \DB::table($this->tableName)->where('id', '=', 1)->first();

        $this->assertEquals('Email-1@example.com', $users->email);
        $this->assertEquals('Record-1', $users->name);
    }
}