<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Illuminate\Http\Request;
use lumilock\lumilock\App\Services\RouteService;

class GatewayController extends Controller
{
    /**
     * The service to consume the authors micro-service
     * @var RouteService
     */
    public $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    /**
     * routesGet.
     *
     */
    public function routesGet(Request $request, $route)
    {
        dd('here');
        if ($request->getHttpHost() === 'localhost:8000') {
            // echo($request->path(), $request->method(), $request->getHttpHost());
            return $this->routeService->route($request->method(), $request->path());
        } else {
            dd($request->path(), $request->method(), $request->getHttpHost());
        }
        
    }
    /**
     * routesGet.
     *
     */
    public function routesPost(Request $request)
    {
        if ($request->getHttpHost() === 'localhost:8000') {
            // echo($request->path(), $request->method(), $request->getHttpHost());
            return $this->routeService->route($request->method(), $request->path(), $request->all());
        } else {
            dd($request->path(), $request->method(), $request->getHttpHost(), $request->all());
        }
    }

}
