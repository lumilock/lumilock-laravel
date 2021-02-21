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
        $router->group(['prefix' => 'auth'], function () use ($router) {
            // ROUTE : api/auth/...

            // Auth functions
            $router->post('login', 'AuthController@login');
            $router->post('logout', 'AuthController@logout');
            $router->post('register', 'AuthController@register'); // ! deprecated
            $router->get('check', 'AuthController@check');

            // Profile
            $router->get('profile', 'UserController@profile');
            $router->put('profile', 'UserController@updateProfile');
            $router->get('profile/tokens', 'UserController@profileTokens');
            $router->delete('profile/tokens', 'UserController@profileDeleteTokens');
            $router->delete('profile/tokens/{tokenId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'UserController@profileDeleteToken');

            // Users
            $router->get('users', 'UserController@allUsers');
            $router->get('users/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'UserController@singleUser');
            $router->post('users', 'AuthController@register');
            $router->put('users/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', function () {
                return 'Not implemented : put /api/auth/users/{id} = update a specific user.';
            });
            $router->delete('users/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', function () {
                return 'Not implemented : delete /api/auth/users/{id} = delete a specific user.';
            });
            $router->get('users/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}/rights', function () {
                return 'Not implemented : get /api/auth/users/{id}/rights = get all rights by services from a user.';
            });
            $router->put('users/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}/rights', function () {
                return 'Not implemented : put /api/auth/users/{id}/rights = update all rights by services from a user.';
            });

            // Services
            $router->get('services', function () {
                return 'Not implemented : get /api/auth/services = get all services.';
            });
            $router->post('services', 'ServiceController@singleService');
            $router->get('services/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', function () {
                return 'Not implemented : get /api/auth/services/{id} = get specific service.';
            });
            $router->put('services/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', function () {
                return 'Not implemented : put /api/auth/services/{id} = update specific service.';
            });
            $router->delete('services/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', function () {
                return 'Not implemented : delete /api/auth/services/{id} = delete specific service.';
            });
            $router->get('services/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}/permissions', function () {
                return 'Not implemented : get /api/auth/services/{id}/permissions = get all permission of a service.';
            });

            // Keys
            $router->get('keys', function () {
                return 'Not implemented : get /api/auth/keys = get all api keys.';
            });
            $router->post('keys', function () {
                return 'Not implemented : post /api/auth/keys = create a new api key.';
            });
            $router->get('keys/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', function () {
                return 'Not implemented : get /api/auth/keys/{id} = get a specific api keys.';
            });
            $router->delete('keys/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', function () {
                return 'Not implemented : delete /api/auth/keys/{id} = remove a specific api keys.';
            });

            // Matches "/api/install
            $router->get('install', 'InstallController@installTest');


            $notFoundController = function () {
                return 'Not Found';
            };

            $router->get('/{route:.*}/', $notFoundController);
            $router->post('/{route:.*}/', $notFoundController);
            $router->put('/{route:.*}/', $notFoundController);
            $router->patch('/{route:.*}/', $notFoundController);
            $router->delete('/{route:.*}/', $notFoundController);
        });
    }
);
