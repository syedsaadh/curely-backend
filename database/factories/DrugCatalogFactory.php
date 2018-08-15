<?php

use Faker\Generator as Faker;

$factory->define(App\Models\DrugCatalog::class, function (Faker $faker) {
    return [
        'name' => $faker->word
    ];
});
