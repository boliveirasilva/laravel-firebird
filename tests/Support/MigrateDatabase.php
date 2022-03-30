<?php

namespace FirebirdTests\Support;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait MigrateDatabase
{
    public function setUp()
    {
        parent::setUp();

        if (! MigrationState::$migrated) {
            $this->dropTables();
            $this->createTables();

            $this->dropProcedure();
            $this->createProcedure();

            MigrationState::$migrated = true;
        }
    }

    public function tearDown()
    {
        DB::select('DELETE FROM "testbench_orders"');
        DB::select('DELETE FROM "testbench_users"');

        // Reset the static ids on the factory, as Firebird <= 3 does not
        // support auto-incrementing ids.
        // TODO: Like to figure out a way of using auto-incrementing ids for
        // newer versions of Firebird, but not ready to drop v2.5 support yet.

        parent::tearDown();
    }

    public function createTables()
    {
        DB::select('CREATE TABLE "testbench_users" (
            "id" INTEGER NOT NULL, 
            "name" VARCHAR(255) NOT NULL, 
            "email" VARCHAR(255) NOT NULL, 
            "password" VARCHAR(255), 
            "city" VARCHAR(255), 
            "state" VARCHAR(255), 
            "post_code" VARCHAR(255), 
            "country" VARCHAR(255), 
            "created_at" TIMESTAMP, 
            "updated_at" TIMESTAMP, 
            "deleted_at" TIMESTAMP
        )');

        DB::select('ALTER TABLE "testbench_users" ADD PRIMARY KEY ("id")');


        DB::select('CREATE TABLE "testbench_orders" (
            "id" INTEGER NOT NULL, 
            "user_id" INTEGER NOT NULL, 
            "name" VARCHAR(255) NOT NULL, 
            "price" INTEGER NOT NULL, 
            "quantity" INTEGER NOT NULL, 
            "created_at" TIMESTAMP, 
            "updated_at" TIMESTAMP, 
            "deleted_at" TIMESTAMP
        )');

        DB::select('ALTER TABLE "testbench_orders" ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY ("user_id") REFERENCES "testbench_users" ("id")');
        DB::select('ALTER TABLE "testbench_orders" ADD PRIMARY KEY ("id")');
    }

    public function dropTables()
    {
        try {
            DB::select('DROP TABLE "testbench_orders"');
        } catch (QueryException $e) {
            // Suppress the "table does not exist" exception, as we want to
            // replicate dropIfExists() functionality without using the Schema
            // class.
            if (! Str::contains($e->getMessage(), 'does not exist')) {
                throw $e;
            }
        }

        try {
            DB::select('DROP TABLE "testbench_users"');
        } catch (QueryException $e) {
            // Suppress the "table does not exist" exception, as we want to
            // replicate dropIfExists() functionality without using the Schema
            // class.
            if (! Str::contains($e->getMessage(), 'does not exist')) {
                throw $e;
            }
        }
    }

    public function createProcedure()
    {
        DB::select(
            'CREATE PROCEDURE MULTIPLY (a INTEGER, b INTEGER)
                RETURNS (result INTEGER)
            AS BEGIN
                result = a * b;
                SUSPEND;
            END'
        );
    }

    public function dropProcedure()
    {
        try {
            DB::select('DROP PROCEDURE MULTIPLY');
        } catch (QueryException $e) {
            // Suppress the "procedure not found" exception, as we want to
            // replicate dropIfExists() functionality without using the Schema
            // class.
            if (! Str::contains($e->getMessage(), 'not found')) {
                throw $e;
            }
        }
    }
}