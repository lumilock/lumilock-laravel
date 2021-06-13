<?php

namespace lumilock\lumilock\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
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

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $uri = 'http://' . $this->faker->ipv4;
        $path = '/api/' . $this->faker->domainWord;
        $address = 'http://www.' . $this->faker->domainName;

        return [
            'name' => $this->faker->company, // app name
            'uri' => $uri, // api server uri
            'secret' => '123456789', // api secret password
            'path' => $path, // api base url
            'address' => $address, // front end url
            'picture' => 'https://source.unsplash.com/random', // app picture
        ];
    }
}