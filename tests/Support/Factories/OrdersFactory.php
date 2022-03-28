<?php

/** @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FirebirdTests\Support\Models\Order::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->numberBetween(1),
        'user_id' => factory(FirebirdTests\Support\Models\User::class)->create(),
        'name' => $faker->word,
        'price' => $faker->numberBetween(1, 200),
        'quantity' => $faker->numberBetween(0, 8),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
