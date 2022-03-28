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
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertFalse(Schema::hasTable('foo'));
    }

    /** @test */
    public function it_has_column()
    {
        $this->assertTrue(Schema::hasColumn('users', 'id'));
        $this->assertFalse(Schema::hasColumn('users', 'foo'));
    }

    /** @test */
    public function it_has_columns()
    {
        $this->assertTrue(Schema::hasColumns('users', ['id', 'country']));
        $this->assertFalse(Schema::hasColumns('users', ['id', 'foo']));
    }

    /** @test */
    public function it_can_create_a_table()
    {
        Schema::dropIfExists('foo');
        $this->assertFalse(Schema::hasTable('foo'));

        Schema::create('foo', function (Blueprint $table) {
            $table->string('bar');
        });
        $this->assertTrue(Schema::hasTable('foo'));

        // Clean up...
        Schema::drop('foo');
    }

    /**
     * @test
     */
    public function it_can_drop_table_if_exists()
    {
        DB::select('RECREATE TABLE "foo" ("id" INTEGER NOT NULL)');
        $this->assertTrue(Schema::hasTable('foo'), 'Failed to recreate foo table');

        Schema::dropIfExists('foo');
        $this->assertFalse(Schema::hasTable('foo'), 'Failed to drop foo table (1st run)');

        // Run again to check exists = false.
        Schema::dropIfExists('foo');
        $this->assertFalse(Schema::hasTable('foo'), 'Failed to drop foo table (2nd run)');
    }

    /** @test */
    public function it_can_drop_table()
    {
        DB::select('RECREATE TABLE "foo" ("id" INTEGER NOT NULL)');
        $this->assertTrue(Schema::hasTable('foo'));

        Schema::drop('foo');
        $this->assertFalse(Schema::hasTable('foo'));
    }
}