<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Illuminate\Http\Request;
use lumilock\lumilock\App\Http\Resources\ServiceUriResource;
use lumilock\lumilock\App\Models\Service;
use lumilock\lumilock\App\Services\RouteService;
use lumilock\lumilock\App\Traits\ApiResponser;

class GatewayController extends Controller
{
    use ApiResponser;
    
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
        // we create a list of slug
        $slug_array = preg_split("/\//", $request->path());
        // we check that we have 2 slug (api / <serviceName>)
        if (count($slug_array) >= 2 && strcmp($slug_array[0], "api") === 0) {
            try{
                $path = '/' . $slug_array[0] . '/' . $slug_array[1]; // we re-create the service path in order to find the uri

                $service = new ServiceUriResource(Service::where('path', '=', $path)->firstOrFail()); // we get uri and secret from database if exist
                $this->routeService->setUri($service->uri);
                $this->routeService->setSecret($service->secret);
                $headers['Authorization'] = $request->header('Authorization');
                return $this->successResponse($this->routeService->route($request->method(), $request->path(), [], $headers));
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'data' => null,
                        'status' => 'NOT_FOUND',
                        'message' => 'Service not found!'
                    ],
                    404
                );
            }
        }
    }

    /**
     * routesGet.
     *
     */
    public function routesPost(Request $request)
    {
        dd($request->path(), $request->method(), $request->getHttpHost(), $request->all());
        return $this->routeService->route($request->method(), $request->path(), $request->all());
    }

}
