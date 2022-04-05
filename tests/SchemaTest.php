<?php

namespace FirebirdTests;

use FirebirdTests\Support\MigrateDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchemaTest extends TestCase
{
    use MigrateDatabase;

    /** @test */
    public function it_has_table()
    {
        $this->assertTrue(Schema::hasTable('TESTBENCH_USERS'));
        $this->assertFalse(Schema::hasTable('FOO'));
    }

    /** @test */
    public function it_has_column()
    {
        $this->assertTrue(Schema::hasColumn('TESTBENCH_USERS', 'ID'));
        $this->assertFalse(Schema::hasColumn('TESTBENCH_USERS', 'FOO'));
    }

    /** @test */
    public function it_has_columns()
    {
        $this->assertTrue(Schema::hasColumns('TESTBENCH_USERS', ['ID', 'COUNTRY']));
        $this->assertFalse(Schema::hasColumns('TESTBENCH_USERS', ['ID', 'FOO']));
    }

    /** @test */
    public function it_can_create_a_table()
    {
        Schema::dropIfExists('FOO');
        $this->assertFalse(Schema::hasTable('FOO'));

        Schema::create('FOO', function (Blueprint $table) {
            $table->string('BAR');
        });
        $this->assertTrue(Schema::hasTable('FOO'));

        // Clean up...
        Schema::drop('FOO');
    }

    /**
     * @test
     */
    public function it_can_drop_table_if_exists()
    {
        DB::select('RECREATE TABLE FOO (ID INTEGER NOT NULL)');
        $this->assertTrue(Schema::hasTable('FOO'), 'Failed to recreate foo table');

        Schema::dropIfExists('FOO');
        $this->assertFalse(Schema::hasTable('FOO'), 'Failed to drop foo table (1st run)');

        // Run again to check exists = false.
        Schema::dropIfExists('FOO');
        $this->assertFalse(Schema::hasTable('FOO'), 'Failed to drop foo table (2nd run)');
    }

    /** @test */
    public function it_can_drop_table()
    {
        DB::select('RECREATE TABLE FOO (ID INTEGER NOT NULL)');
        $this->assertTrue(Schema::hasTable('FOO'));

        Schema::drop('FOO');
        $this->assertFalse(Schema::hasTable('FOO'));
    }
}