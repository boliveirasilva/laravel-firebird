<?php

namespace FirebirdTests;

use Carbon\Carbon;
use FirebirdTests\Support\MigrateDatabase;
use FirebirdTests\Support\Models\TestbenchOrder;
use FirebirdTests\Support\Models\TestbenchUser;

class ModelTest extends TestCase
{
    use MigrateDatabase;

    /** @test */
    public function it_can_create_a_record()
    {
        $id = 100; # TODO: make possible to use autoincrement.

        $user = TestbenchUser::create($fields = [
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

    /** @test */
    public function it_can_create_a_order_record_with_composite_primary_key()
    {
        // $id = 4251;
        $user_id = 15;
        TestbenchUser::create([
            'ID' => $user_id,
            'NAME' => 'Anna',
            'EMAIL' => 'anna@example.com',
            'CITY' => 'Sydney',
            'STATE' => 'New South Wales',
            'POST_CODE' => '2000',
            'COUNTRY' => 'Australia'
        ]);

        $pk_data = [
            'ID' => 4251,
            'USER_ID' => $user_id,
        ];

        $order = TestbenchOrder::create($fields = array_merge(
            $pk_data,
            [
                'NAME' => 'Beta Tester',
                'PRICE' => 16.27,
                'QUANTITY' => 5,
                'CREATED_AT' => Carbon::now()->toDateTimeString(),
                'UPDATED_AT' => Carbon::now()->toDateTimeString(),
            ]
        ));

        $order = TestbenchOrder::where($pk_data)->first();

        $this->assertInstanceOf(TestbenchOrder::class, $order);

        // Check all fields have been persisted the model.
        foreach ($fields as $key => $value) {
            $this->assertEquals($value, $order->{$key});
        }
    }
}