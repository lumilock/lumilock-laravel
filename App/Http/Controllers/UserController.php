<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use  lumilock\lumilock\App\Models\User;
use lumilock\lumilock\App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function profile()
    {
        // , 'client ip' => $_SERVER['REMOTE_ADDR'], 'host name' => gethostname(), 'browser' => $_SERVER['HTTP_USER_AGENT']
        return response()->json(
            [
                'data' => new UserResource(Auth::user()),
                'status' => 'SUCCESS',
                'message' => 'Data of the current user.'
            ],
            200
        );
    }

    /**
     * Get all User.
     *
     * @return Response
     */
    public function allUsers()
    {
        return response()->json(
            [
                'data' =>  UserResource::collection(User::all()),
                'status' => 'SUCCESS',
                'message' => 'List of all users.'
            ],
            200
        );
    }

    /**
     * Get one user.
     *
     * @return Response
     */
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(
                [
                    'data' => new UserResource($user),
                    'status' => 'SUCCESS',
                    'message' => 'Data of the user ' . $id . '.'
                ],
                200
            );
        } catch (\Exception $e) {

            return response()->json(
                [
                    'data' => null,
                    'status' => 'NOT_FOUND',
                    'message' => 'user not found!'
                ],
                404
            );
        }
    }
}
