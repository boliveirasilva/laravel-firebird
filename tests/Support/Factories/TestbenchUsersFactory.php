<?php

/** @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FirebirdTests\Support\Models\TestbenchUser::class, function (Faker\Generator $faker) {
    return [
        'ID' => $faker->unique()->numberBetween(1),
        'NAME' => $faker->name,
        'EMAIL' => $faker->email,
        'PASSWORD' => $faker->password,
        'CITY' => $faker->city,
        'STATE' => $faker->state,
        'POST_CODE' => $faker->postcode,
        'COUNTRY' => $faker->country,
        'CREATED_AT' => $faker->dateTime,
        'UPDATED_AT' => $faker->dateTime,
    ];
});