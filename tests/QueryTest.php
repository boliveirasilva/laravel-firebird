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
        $users = DB::table('TESTBENCH_USERS')->get();

        $this->assertCount(3, $users);
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertTrue(is_object($users->first()));
        $this->assertTrue(is_array($users->toArray()));

        $orders = DB::table('TESTBENCH_ORDERS')->get();

        $this->assertCount(3, $orders);
        $this->assertInstanceOf(Collection::class, $orders);
        $this->assertTrue(is_object($orders->first()));
        $this->assertTrue(is_array($orders->toArray()));
    }

    /** @test */
    public function it_can_select()
    {
        factory(TestbenchUser::class)->create([
            'NAME' => 'Anna',
            'CITY' => 'Sydney',
            'COUNTRY' => 'Australia',
        ]);

        $result = DB::table('TESTBENCH_USERS')
            ->select(['NAME', 'CITY', 'COUNTRY'])
            ->first();

        $this->assertCount(3, (array) $result);

        $this->assertObjectHasAttribute('NAME', $result);
        $this->assertObjectHasAttribute('CITY', $result);
        $this->assertObjectHasAttribute('COUNTRY', $result);

        $this->assertEquals('Anna', $result->NAME);
        $this->assertEquals('Sydney', $result->CITY);
        $this->assertEquals('Australia', $result->COUNTRY);
    }

    /** @test */
    public function it_can_select_with_aliases()
    {
        factory(TestbenchUser::class)->create([
            'NAME' => 'Anna',
            'CITY' => 'Sydney',
            'COUNTRY' => 'Australia',
        ]);

        $result = DB::table('TESTBENCH_USERS')
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
        factory(TestbenchOrder::class, 1)->create(['PRICE' => 10]);
        factory(TestbenchOrder::class, 10)->create(['PRICE' => 50]);
        factory(TestbenchOrder::class, 5)->create(['PRICE' => 100]);

        $results = DB::table('TESTBENCH_ORDERS')->select('PRICE')->distinct()->get();

        $this->assertCount(3, $results);
    }

    /** @test */
    public function it_can_filter_where_with_results()
    {
        factory(TestbenchUser::class, 5)->create(['NAME' => 'Frank']);
        factory(TestbenchUser::class, 2)->create(['NAME' => 'Inigo']);
        factory(TestbenchUser::class, 7)->create(['NAME' => 'Ashley']);

        $results = DB::table('TESTBENCH_USERS')
            ->where('NAME', 'Frank')
            ->get();

        $this->assertCount(5, $results);
        $this->assertCount(1, $results->pluck('NAME')->unique());
        $this->assertEquals('Frank', $results->random()->NAME);
    }

    /** @test */
    public function it_can_filter_where_without_results()
    {
        factory(TestbenchUser::class, 25)->create();

        $results = DB::table('TESTBENCH_USERS')
            ->where('ID', 26)
            ->get();

        $this->assertCount(0, $results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertEquals([], $results->toArray());
        $this->assertNull($results->first());
    }
}
