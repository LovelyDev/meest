<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(\App\Room::class, function (Faker $faker) {
    return [
        'official' => false,
        'registered' => false,
        'title' => $faker->sentence(),
        'description' => $faker->paragraph(),
        'limit' => 12,
        'language' => 'en',
        'user_id' => 1,
        'created_at' => $faker->dateTime($max = 'now'),
        'updated_at' => $faker->dateTime($max = 'now'),
    ];
});
