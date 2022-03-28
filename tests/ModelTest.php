<?php

namespace FirebirdTests;

use Carbon\Carbon;
use FirebirdTests\Support\MigrateDatabase;
use FirebirdTests\Support\Models\User;

class ModelTest extends TestCase
{
    use MigrateDatabase;

    /** @test */
    public function it_can_create_a_record()
    {
        $id = 100; # TODO: make possible to use autoincrement.

        User::create($fields = [
            'id' => $id,
            'name' => 'Anna',
            'email' => 'anna@example.com',
            'city' => 'Sydney',
            'state' => 'New South Wales',
            'post_code' => '2000',
            'country' => 'Australia',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        $user = User::find($id);

        $this->assertInstanceOf(User::class, $user);

        // Check all fields have been persisted the model.
        foreach ($fields as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }
}