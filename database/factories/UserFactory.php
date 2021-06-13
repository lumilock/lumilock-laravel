<?php

namespace lumilock\lumilock\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use lumilock\lumilock\App\Models\User;

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

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $first_name = $this->faker->firstName;
        $last_name = $this->faker->lastName;
        $login = strtolower($first_name) . '.' . strtolower($last_name);

        return [
            'login' => $login,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $this->faker->safeEmail,
            'password' => Hash::make($this->faker->password),
            'picture' => 'https://source.unsplash.com/random', // app picture
        ];
    }
}