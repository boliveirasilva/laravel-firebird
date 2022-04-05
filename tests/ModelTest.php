<?php

namespace FirebirdTests;

use Carbon\Carbon;
use FirebirdTests\Support\MigrateDatabase;
use FirebirdTests\Support\Models\TestbenchUser;

class ModelTest extends TestCase
{
    use MigrateDatabase;

    /** @test */
    public function it_can_create_a_record()
    {
        $id = 100; # TODO: make possible to use autoincrement.

        TestbenchUser::create($fields = [
            'ID' => $id,
            'NAME' => 'Anna',
            'EMAIL' => 'anna@example.com',
            'CITY' => 'Sydney',
            'STATE' => 'New South Wales',
            'POST_CODE' => '2000',
            'COUNTRY' => 'Australia',
            'CREATED_AT' => Carbon::now()->toDateTimeString(),
            'UPDATED_AT' => Carbon::now()->toDateTimeString(),
        ]);

        $user = TestbenchUser::find($id);

        $this->assertInstanceOf(TestbenchUser::class, $user);

        // Check all fields have been persisted the model.
        foreach ($fields as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }
}