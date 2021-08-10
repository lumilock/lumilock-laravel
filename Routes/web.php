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
            $router->get('ping', function () {
                return 'lumilock auth : >>> pong!';
            });

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
            $router->group(['middleware' => ['Lumilock-permissions:/api/auth,access', 'Lumilock-permissions:/api/auth,users']], function () use ($router) {

                $router->get('users', 'UserController@allUsers');
                $router->get('users/{id:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'UserController@singleUser');
                $router->post('users', 'AuthController@register');
                $router->get('users/number', 'UserController@userNumber');
                $router->put('users/{userId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'UserController@updateUser');
                $router->delete('users/{userId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'UserController@deleteUser');
                $router->get('users/{userId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}/rights', 'UserController@rightsUser');
                $router->put('users/{userId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}/rights', 'UserController@updateRightsUser');
            });

            // Services
            $router->get('services', 'ServiceController@allServices');
            $router->post('services/getByPath', 'ServiceController@getServiceByPath');
            $router->group(['middleware' => ['Lumilock-permissions:/api/auth,access', 'Lumilock-permissions:/api/auth,services']], function () use ($router) {

                $router->post('services', 'ServiceController@store');
                $router->get('services/number', 'ServiceController@serviceNumber');
                $router->get('services/{serviceId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'ServiceController@singleService');
                $router->put('services/{serviceId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'ServiceController@updateService');
                $router->delete('services/{serviceId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}', 'ServiceController@deleteService');
                $router->get('services/{serviceId:[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}}/permissions', 'ServiceController@servicePermissions');
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
        });
        $notFoundController = function () {
            return 'Not Found';
        };
        $router->get('/{route:.*}/', $notFoundController);
        $router->post('/{route:.*}/', $notFoundController);
        $router->put('/{route:.*}/', $notFoundController);
        $router->patch('/{route:.*}/', $notFoundController);
        $router->delete('/{route:.*}/', $notFoundController);
    }
);
