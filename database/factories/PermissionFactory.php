<?php

namespace lumilock\lumilock\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use lumilock\lumilock\App\Models\Permission;
use lumilock\lumilock\App\Models\Service;

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

class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'service_id' => function() {
                return Service::factory()->create()->id;
            },
            'name' => $this->faker->word, // permission name
        ];
    }
}