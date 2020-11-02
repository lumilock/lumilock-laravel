<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
$this->app->router->group(
    [
        'prefix' => 'api',
        'namespace' => 'lumilock\lumilock\App\Http\Controllers'
    ],
    function ($router) {
        // Matches "/api/register
        $router->post('register', 'AuthController@register');

        // Matches "/api/login
        $router->post('login', 'AuthController@login');

        // Matches "/api/logout
        $router->post('logout', 'AuthController@logout');

        // Matches "/api/profile
        $router->get('profile', 'UserController@profile');
    
        // Matches "/api/users/1 
        //get one user by id
        $router->get('users/{id}', 'UserController@singleUser');
    
        // Matches "/api/users
        $router->get('users', 'UserController@allUsers');

        // Matches "/api/install
        $router->get('install', 'InstallController@installTest');


        $router->get('/test', function () {
            return 'Hello Worlds';
        });
    }
);
