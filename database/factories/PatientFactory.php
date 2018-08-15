<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Patients::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'mobile' => $faker->regexify('^[789]\d{9}$'),
        'gender' => $faker->randomElement($array = array('male', 'female')),
    ];
});
