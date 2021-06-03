<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\JType;
use Faker\Generator as Faker;

$factory->define(JType::class, function (Faker $faker) {
    return [
        'Name' => $faker->jobTitle,
    ];
});
