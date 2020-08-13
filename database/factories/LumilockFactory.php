<?php

namespace lumilock\lumilock\database\factories;

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\lumilock\lumilock\App\Models\User::class, function (Faker $faker) {
    return [
        'login' => "first-name"."\$split\$"."last-name",
        'first_name' => "first name",
        'last_name' => "last name",
        'email' => "email@factory.com",
        'password' => app('hash')->make("password")
    ];
});