<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use lumilock\lumilock\App\Models\User;

class UserTest extends TestCase
{
    public function test_formating_user_names()
    {
        $first_name = "fiRst  -  n0+Am54é-name";
        $last_name = "l@|/aSt_01NaME.name.";
        $email = "eMaiL@test.com";
        $password = "password";

        //user creation
        $user = factory(User::class)->create([
            'login' => $first_name."\$split\$".$last_name,
            'first_name' => $first_name,
            'last_name' =>$last_name,
            'email' => $email,
            'password' => app('hash')->make($password)
        ]);

        //verification of the format
        $this->seeInDatabase('users', [
            'login' => 'first-name-name.last-name-name',
            'first_name' => 'First-Namé-Name',
            'last_name' => 'Last-Name-Name',
            'email' => 'eMaiL@test.com'
        ]);

        //try with a point
        $user->update([
            'login' => "First-Namé-Name"."."."Last-Name-Name",
        ]);
        //verification of the format
        $this->seeInDatabase('users', [
            'login' => 'first-name-name.last-name-name2',
            'first_name' => 'First-Namé-Name',
            'last_name' => 'Last-Name-Name',
            'email' => 'eMaiL@test.com'
        ]);

        //delete the userTest
        $user->delete();

        $last_name = "icil@|/aSt_01NaME.name";

        //user creation with bad login so return Exception
        $twoSpliteUser = factory(User::class)->create([
            'login' => $first_name."\$split\$".$last_name,
            'first_name' => $first_name,
            'last_name' =>$last_name,
            'email' => $email,
            'password' => app('hash')->make($password)
        ]);

        //delete the userTest
        $twoSpliteUser->delete();

        $this->expectException(Exception::class);
        //user creation with bad login so return Exception
        $badUser = factory(User::class)->create([
            'login' => $first_name."\$split\$"."\$split\$".$last_name,
            'first_name' => $first_name,
            'last_name' =>$last_name,
            'email' => $email,
            'password' => app('hash')->make($password)
        ]);
    }


    public function test_auth_function() {
        //get the user object of currant auth
        $auth = Auth::user();
        //no user so wait a null auth
        $this->assertEquals(null, $auth);
        //create user
        $user = factory(User::class)->create();
        //get the user object of currant auth
        $auth = Auth::user();
        //User isn't connect so wait a null auth
        $this->assertEquals(null, $auth);
        //connect user
        $this->be($user);
        //get the user object of currant auth
        $auth = Auth::user();
        //User created and connected so wait the same id for $user and $auth
        $this->assertEquals($user->id, $auth->id);
        //clean the database
        $user->delete();
    }
}
