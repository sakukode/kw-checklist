<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
 */

$factory->define('App\Checklist', function (Faker\Generator $faker) {
    return [
        'object_domain' => $faker->domainWord,
        'object_id'     => $faker->randomDigitNotNull,
        'description'   => $faker->sentence,
        'is_completed'  => false,
        'template_id'	=> null,
    ];
});
