<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use lumilock\lumilock\App\Http\Resources\ServiceResource;
use lumilock\lumilock\App\Http\Resources\ServiceUriResource;
use lumilock\lumilock\App\Models\Service;
use GuzzleHttp\Client;
use lumilock\lumilock\App\Services\RouteService;
use lumilock\lumilock\App\Traits\ApiResponser;

class ServiceController extends Controller
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
     * Get all Services. // TODO PERMISSIONS
     *
     * @return Response
     */
    public function allServices()
    {
        return response()->json(
            [
                'data' =>  ServiceResource::collection(Service::all()),
                'status' => 'SUCCESS',
                'message' => 'List of all services.'
            ],
            200
        );
    }

    /**
     * Get one service by path.
     *
     * @return Response
     */
    public function getServiceByPath(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'path' => 'required',
        ]);

        try {

            $service = Service::where('path', '=', $request->input('path'))->firstOrFail();
            return response()->json(
                [
                    'data' => new ServiceUriResource($service),
                    'status' => 'SUCCESS',
                    'message' => 'Data of the service ' . $request->input('path') . '.'
                ],
                200
            );
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
    /**
     * Store a new service. // TODO PERMISSIONS
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|max:150|string',
            'uri' => 'required|max:190|string|unique:services',
            'secret' => 'required|max:100|string',
            'path' => 'required|max:190|string|unique:services',
            'picture_512' => 'required|max:255|string',
            'picture_256' => 'required|max:255|string',
            'picture_128' => 'required|max:255|string',
            'picture_64' => 'required|max:255|string',
            'picture_32' => 'required|max:255|string',
            'picture_16' => 'required|max:255|string',
        ]);

        try {

            $service_response = (Object) $this->routeService->route(
                'GET',
                $request->input('uri') . $request->input('path') . '/permissions',
                [],
                ['Authorization_secret' => $request->input('secret')]);
            $service_content = json_decode($service_response->content);
            if (!$service_content) {
                throw new Exception("Error Processing Request", 1);
            }
            $permissions_list = $service_content->data;
            dd($permissions_list);

            $service = new Service();
            $service->name = $request->input('name');
            $service->uri = $request->input('uri');
            $service->secret = $request->input('secret');
            $service->path = $request->input('path');
            $service->picture_512 = $request->input('picture_512');
            $service->picture_256 = $request->input('picture_256');
            $service->picture_128 = $request->input('picture_128');
            $service->picture_64 = $request->input('picture_64');
            $service->picture_32 = $request->input('picture_32');
            $service->picture_16 = $request->input('picture_16');

            $service->save();

            //return successful response
            return response()->json(
                [
                    'data' => new ServiceResource($service),
                    'status' => 'CREATED',
                    'message' => 'New service has been created!'
                ],
                201
            );
        } catch (\Exception $e) {
            // return error message
            return response()->json(
                [
                    'data' => $e,
                    'status' => 'FAILED',
                    'message' => 'Service registration Failed!'
                ],
                409
            );
        }
    }

    /**
     * Get one service by id.
     *
     * @return Response
     */
    public function singleService($serviceId)
    {
        try {
            $service = Service::findOrFail($serviceId);

            return response()->json(
                [
                    'data' => new ServiceResource($service),
                    'status' => 'SUCCESS',
                    'message' => 'Data of the service ' . $serviceId . '.'
                ],
                200
            );
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

    /**
     * Update info of an service.
     * ! warning this function is only accessible by a super admin PERMISSIONS
     *
     * @return Response
     */
    public function updateService(Request $request, $serviceId)
    {
        // we trim and remove null value from our inputs
        $inputs = array_filter(array_map('trim', $request->all()), 'strlen');

        // validate incoming request 
        $this->validate($request, [
            'name' => 'max:150|string',
            'uri' => "max:190|string|unique:services,uri,{$serviceId}",
            'secret' => 'max:100|string',
            'path' => "max:190|string|unique:services,path,{$serviceId}",
            'picture_512' => 'max:255|string',
            'picture_256' => 'max:255|string',
            'picture_128' => 'max:255|string',
            'picture_64' => 'max:255|string',
            'picture_32' => 'max:255|string',
            'picture_16' => 'max:255|string',
        ]);

        // get service we want to update
        $updateService = Service::find($serviceId);
        // merge fillable and hidden value in an array and switch value to become keys [0 => password] -> [password => ""]
        $columns = array_fill_keys($updateService->getFillable(), '');
        // Columns that we can update (only fillable or hidden columns)
        $filtredColumns = array_intersect_key($inputs, $columns);
        // we update profile data
        $updateService->fill($filtredColumns);
        $updateService->save();

        // sending a response to the user
        return response()->json(
            [
                'data' => $updateService,
                'status' => 'SUCCESS',
                'message' => 'Data of the current service.'
            ],
            200
        );
    }
}
