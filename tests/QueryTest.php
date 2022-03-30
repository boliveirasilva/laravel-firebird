<?php

namespace FirebirdTests;

use FirebirdTests\Support\MigrateDatabase;
use FirebirdTests\Support\Models\TestbenchOrder;
use FirebirdTests\Support\Models\TestbenchUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QueryTest extends TestCase
{
    use MigrateDatabase;

    /** @test */
    public function it_has_the_correct_connection()
    {
        $this->assertEquals('firebird', DB::getDefaultConnection());
    }

    /** @test */
    public function it_can_get()
    {
        factory(TestbenchOrder::class, 3)->create();
        $users = DB::table('testbench_users')->get();

        $this->assertCount(3, $users);
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertTrue(is_object($users->first()));
        $this->assertTrue(is_array($users->toArray()));

        $orders = DB::table('testbench_orders')->get();

        $this->assertCount(3, $orders);
        $this->assertInstanceOf(Collection::class, $orders);
        $this->assertTrue(is_object($orders->first()));
        $this->assertTrue(is_array($orders->toArray()));
    }

    /** @test */
    public function it_can_select()
    {
        factory(TestbenchUser::class)->create([
            'name' => 'Anna',
            'city' => 'Sydney',
            'country' => 'Australia',
        ]);

        $result = DB::table('testbench_users')
            ->select(['name', 'city', 'country'])
            ->first();

        $this->assertCount(3, (array) $result);

        $this->assertObjectHasAttribute('name', $result);
        $this->assertObjectHasAttribute('city', $result);
        $this->assertObjectHasAttribute('country', $result);

        $this->assertEquals('Anna', $result->name);
        $this->assertEquals('Sydney', $result->city);
        $this->assertEquals('Australia', $result->country);
    }

    /** @test */
    public function it_can_select_with_aliases()
    {
        factory(TestbenchUser::class)->create([
            'name' => 'Anna',
            'city' => 'Sydney',
            'country' => 'Australia',
        ]);

        $result = DB::table('testbench_users')
            ->select([
                'name as USER_NAME',
                'city as user_city',
                'country as User_Country',
            ])
            ->first();

        $this->assertCount(3, (array) $result);

        $this->assertObjectHasAttribute('USER_NAME', $result);
        $this->assertObjectHasAttribute('user_city', $result);
        $this->assertObjectHasAttribute('User_Country', $result);

        $this->assertEquals('Anna', $result->USER_NAME);
        $this->assertEquals('Sydney', $result->user_city);
        $this->assertEquals('Australia', $result->User_Country);
    }

    /** @test */
    public function it_can_select_distinct()
    {
        factory(TestbenchOrder::class, 1)->create(['price' => 10]);
        factory(TestbenchOrder::class, 10)->create(['price' => 50]);
        factory(TestbenchOrder::class, 5)->create(['price' => 100]);

        $results = DB::table('testbench_orders')->select('price')->distinct()->get();

        $this->assertCount(3, $results);
    }

    /** @test */
    public function it_can_filter_where_with_results()
    {
        factory(TestbenchUser::class, 5)->create(['name' => 'Frank']);
        factory(TestbenchUser::class, 2)->create(['name' => 'Inigo']);
        factory(TestbenchUser::class, 7)->create(['name' => 'Ashley']);

        $results = DB::table('testbench_users')
            ->where('name', 'Frank')
            ->get();

        $this->assertCount(5, $results);
        $this->assertCount(1, $results->pluck('name')->unique());
        $this->assertEquals('Frank', $results->random()->name);
    }

    /** @test */
    public function it_can_filter_where_without_results()
    {
        factory(TestbenchUser::class, 25)->create();

        $results = DB::table('testbench_users')
            ->where('id', 26)
            ->get();

        $this->assertCount(0, $results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertEquals([], $results->toArray());
        $this->assertNull($results->first());
    }
}
