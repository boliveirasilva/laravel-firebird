<?php

// namespace FirebirdTests\Support\Factories;
//
// use Carbon\Carbon;
// use FirebirdTests\Support\Models\User;
// use Illuminate\Database\Eloquent\Factory;
//
// class UsersFactory extends Factory
// {
//     protected $model = User::class;
//
//     public static $id = 1;
//
//     public function definition()
//     {
//         return [
//             'id' => self::$id++,
//             'name' => $this->faker->name,
//             'email' => $this->faker->email,
//             'city' => $this->faker->city,
//             'state' => $this->faker->state,
//             'post_code' => $this->faker->postcode,
//             'country' => $this->faker->country,
//             'created_at' => Carbon::now(),
//             'updated_at' => Carbon::now(),
//         ];
//     }
// }

/** @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FirebirdTests\Support\Models\User::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->numberBetween(1),
        'name' => $faker->name,
        'email' => $faker->email,
        'city' => $faker->city,
        'state' => $faker->state,
        'post_code' => $faker->postcode,
        'country' => $faker->country,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});