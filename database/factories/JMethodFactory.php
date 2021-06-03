<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\JMethod;
use Faker\Generator as Faker;

$factory->define(JMethod::class, function (Faker $faker) {
    return [
        'Name' => $faker->sentence(3),
    ];
});
