<?php

namespace lumilock\lumilock\App\Http\Controllers;

use \GuzzleHttp\Psr7\Request as GRequest;
use \GuzzleHttp\Client;

class InstallController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * test la fonction install avec guzzle.
     *
     * @return Response
     */
    public function installTest()
    {

        $client = new Client([
            'base_uri' => 'http://localhost:8001',
            'defaults' => [
                'exceptions' => false
            ]
        ]);;
        $request = new GRequest('GET', '/api/auditLaitCru/install');
        $promise = $client->sendAsync($request)->then(
            function ($response) {
                return $response->getBody()->getContents();
            }, function ($exception) {
                return $exception->getMessage();
            }
        );
        $responseJson = $promise->wait();

        return response()->json(
            [
                'data' => json_decode($responseJson),
                'status' => 'SUCCESS',
                'message' => 'This is installs info.'
            ],
            200
        );
    }

}
