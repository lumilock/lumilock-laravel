<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use lumilock\lumilock\App\Http\Resources\ServiceResource;
use lumilock\lumilock\App\Http\Resources\ServiceUriResource;
use lumilock\lumilock\App\Models\Service;
use lumilock\lumilock\App\Models\Permission;
use lumilock\lumilock\App\Models\User;
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
        $this->middleware('auth');
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
                'data' =>  ServiceResource::collection(Service::has('access')->get()),
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
            'address' => 'required|max:190|string|unique:services',
            'picture' => 'required|max:255|string',
        ]);

        try {
            // We get permissions form the services config route
            $service_response = (object) $this->routeService->route(
                'GET',
                $request->input('uri') . $request->input('path') . '/permissions',
                [],
                ['Authorization_secret' => $request->input('secret')]
            );
            $service_content = json_decode($service_response->content);
            if (!$service_content) {
                throw new Exception("Error Processing Request (no permissions founded)", 1);
            }
            // Checking if there is the app access permission else adding it
            $permissions_list = $service_content->data;
            if (!in_array("access", $permissions_list)) {
                array_push($permissions_list, "access");
            }

            $service = new Service();
            $service->name = $request->input('name');
            $service->uri = $request->input('uri');
            $service->secret = $request->input('secret');
            $service->path = $request->input('path');
            $service->address = $request->input('address');
            $service->picture = $request->input('picture');

            $service->save();

            $permissions_saved = [];
            // store all permissions
            foreach ($permissions_list as &$value) {
                $permission = new Permission();
                $permission->service()->associate($service);
                $permission->name = $value;
                $permission->save();

                array_push($permissions_saved, $permission->id);
            }
            // https://www.php.net/manual/fr/control-structures.foreach.php
            unset($value); // we remove reference

            $users = User::all();
            foreach ($users as &$user) {
                $user->permissions()->attach($permissions_saved);
                $user->save();
            }
            unset($user); // we remove reference
            unset($permission); // we remove reference

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
            dd($e);
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
            'address' => "max:190|string|unique:services,address,{$serviceId}",
            'picture' => 'max:255|string',
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

    /**
     * Function that return all permissions of a specific service find by id
     * @param String $serviceID : id of the service
     */
    public function servicePermissions($serviceId)
    {
        try {
            $service = Service::findOrFail($serviceId);
            $permissions = $service->permissions->pluck('name', 'id');
            return response()->json(
                [
                    'data' => $permissions,
                    'status' => 'SUCCESS',
                    'message' => 'Permissions of the service ' . $serviceId . '.'
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
}
