<?php

/** @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FirebirdTests\Support\Models\TestbenchOrder::class, function (Faker\Generator $faker) {
    return [
        'ID' => $faker->unique()->numberBetween(1),
        'USER_ID' => factory(FirebirdTests\Support\Models\TestbenchUser::class)->create(),
        'NAME' => $faker->word,
        'PRICE' => $faker->numberBetween(1, 200),
        'QUANTITY' => $faker->numberBetween(0, 8),
        'CREATED_AT' => $faker->dateTime,
        'UPDATED_AT' => $faker->dateTime,
    ];
});
