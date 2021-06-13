<?php

namespace lumilock\lumilock\database\seeds;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use lumilock\lumilock\App\Models\Permission;
use lumilock\lumilock\App\Models\Right;
use lumilock\lumilock\App\Models\Service;
use lumilock\lumilock\App\Models\User;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creating an initial services
        $service = Service::factory()
            ->create([
                'name' => 'Lumilock Auth Settings',
                'uri' => 'Lumilock Auth Settings',
                'secret' => 'Lumilock Auth Settings',
                'path' => 'Lumilock Auth Settings',
                'address' => 'Lumilock Auth Settings',
                'picture' => 'Lumilock Auth Settings',
            ]);

        // Init Permissions of this service
        $permissions = Permission::factory(4)
            ->state(new Sequence(
                ['name' => 'Access'], // Give permission to access to the full service (auth admin)
                ['name' => 'Users'], // Give permission to manage all users
                ['name' => 'Services'], // Give permission to manage all services
                ['name' => 'API keys'] // Give permission to manage all api keys
            ))
            ->create([
                'service_id' => $service->id // Link these permissions to the previous Service 
            ]);

        // Init the Auth user
        $auth = User::factory()
            ->create([
                'login' => 'admin.admin',
                'first_name' => 'admin',
                'last_name' => 'admin',
                'password' => Hash::make('toto')
            ]);

        // Create a random user
        $rndUser = User::factory()->create();

        foreach ($permissions as $permission) {
            // Create each rights for the Auth
            Right::factory()
                ->create([
                    'user_id' => $auth->id,
                    'permission_id' => $permission->id,
                    'is_active' => True,
                ]);

            // Create each rights for a random user
            Right::factory()
                ->create([
                    'user_id' => $rndUser->id,
                    'permission_id' => $permission->id,
                ]);
        }
    }
}
