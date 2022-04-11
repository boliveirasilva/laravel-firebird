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
        DB::select('DELETE FROM TESTBENCH_ORDERS');
        DB::select('DELETE FROM TESTBENCH_USERS');

        // Reset the static ids on the factory, as Firebird <= 3 does not
        // support auto-incrementing ids.
        // TODO: Like to figure out a way of using auto-incrementing ids for
        // newer versions of Firebird, but not ready to drop v2.5 support yet.

        parent::tearDown();
    }

    public function createTables()
    {
        DB::select('CREATE TABLE TESTBENCH_USERS (
            ID INTEGER NOT NULL, 
            NAME VARCHAR(255) NOT NULL, 
            EMAIL VARCHAR(255) NOT NULL, 
            PASSWORD VARCHAR(255), 
            CITY VARCHAR(255), 
            STATE VARCHAR(255), 
            POST_CODE VARCHAR(255), 
            COUNTRY VARCHAR(255), 
            CREATED_AT TIMESTAMP, 
            UPDATED_AT TIMESTAMP, 
            DELETED_AT TIMESTAMP
        )');

        DB::select('ALTER TABLE TESTBENCH_USERS ADD PRIMARY KEY (ID)');


        DB::select('CREATE TABLE TESTBENCH_ORDERS (
            ID INTEGER NOT NULL, 
            USER_ID INTEGER NOT NULL, 
            NAME VARCHAR(255) NOT NULL, 
            PRICE FLOAT NOT NULL, 
            QUANTITY INTEGER NOT NULL, 
            CREATED_AT TIMESTAMP, 
            UPDATED_AT TIMESTAMP, 
            DELETED_AT TIMESTAMP
        )');

        DB::select('ALTER TABLE TESTBENCH_ORDERS ADD CONSTRAINT ORDERS_USER_ID_FOREIGN FOREIGN KEY (USER_ID) REFERENCES TESTBENCH_USERS (ID)');
        DB::select('ALTER TABLE TESTBENCH_ORDERS ADD PRIMARY KEY (ID, USER_ID)');
    }

    public function dropTables()
    {
        try {
            DB::select('DROP TABLE TESTBENCH_ORDERS');
        } catch (QueryException $e) {
            // Suppress the "table does not exist" exception, as we want to
            // replicate dropIfExists() functionality without using the Schema
            // class.
            if (! Str::contains($e->getMessage(), 'does not exist')) {
                throw $e;
            }
        }

        try {
            DB::select('DROP TABLE TESTBENCH_USERS');
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