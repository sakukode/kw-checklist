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

$factory->define('App\Item', function (Faker\Generator $faker) {
    return [        
        'description'   => $faker->sentence,
        'due'			=> date("Y-m-d H:i:s"),
        'urgency'		=> $faker->randomDigitNotNull,
        'assignee_id'   => $faker->randomDigitNotNull,
        'checklist_id'	=> $faker->randomDigitNotNull,
    ];
});
