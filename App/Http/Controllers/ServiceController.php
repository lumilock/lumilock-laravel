<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Illuminate\Http\Request;
use lumilock\lumilock\App\Http\Resources\ServiceUriResource;
use lumilock\lumilock\App\Models\Service;

class ServiceController extends Controller
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
     * Get all User.
     *
     * @return Response
     */
    // public function allUsers()
    // {
    //     return response()->json(
    //         [
    //             'data' =>  UserResource::collection(User::all()),
    //             'status' => 'SUCCESS',
    //             'message' => 'List of all users.'
    //         ],
    //         200
    //     );
    // }

    /**
     * Get one user.
     *
     * @return Response
     */
    public function singleService(Request $request)
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
}
