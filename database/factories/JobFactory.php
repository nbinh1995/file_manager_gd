<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Customer;
use App\Models\JMethod;
use App\Models\Job;
use App\Models\JType;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Job::class, function (Faker $faker) {
    return [
        'Name' => 'CV-'. Str::random(5),
        'CustomerID' => Customer::all()->random(1)->first()->ID,
        'TypeID' => JType::all()->random(1)->first()->ID,
        'StartDate' => $faker->dateTimeBetween('-30 days'),
        'RealJob' => random_int(0,1),
        'Deadline' => $faker->dateTimeBetween('now', '+30 days'),
        'Price' => $usdPrice = $faker->randomNumber(4),
        'PriceYen' => $usdPrice * 103,
        'MethodID' => JMethod::all()->random(1)->first()->ID,
        'Paydate' => $faker->dateTimeBetween('+5 days', '+30 days'),
        'FinishDate' => $faker->dateTimeBetween('+5 days', '+30 days'),
        'Paid' => $faker->boolean,
        'Note' => $faker->sentence,
    ];
});
