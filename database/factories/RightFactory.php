<?php

namespace lumilock\lumilock\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use lumilock\lumilock\App\Models\Permission;
use lumilock\lumilock\App\Models\Right;
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

class RightFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Right::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function() {
                return User::factory()->create()->id;
            },
            'permission_id' => function() {
                return Permission::factory()->create()->id;
            },
            'is_active' => $this->faker->boolean, // permission name
        ];
    }
}