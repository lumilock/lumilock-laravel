<?php

namespace lumilock\lumilock\App\Http\Controllers;

use lumilock\lumilock\App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//import auth facades
use Illuminate\Support\Facades\Auth;
use lumilock\lumilock\App\Http\Resources\UserResource;
use lumilock\lumilock\App\Models\User;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(
                [
                    'data' => new UserResource($user),
                    'status' => 'CREATED',
                    'message' => 'New user has been created!'
                ],
                201
            );
        } catch (\Exception $e) {
            //return error message
            return response()->json(
                [
                    'data' => null,
                    'status' => 'FAILED',
                    'message' => 'User Registration Failed!'
                ],
                409
            );
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(
                [
                    'data' => null,
                    'status' => 'UNAUTHORIZED',
                    'message' => 'Unauthorized'
                ],
                401
            );
        }
        return response()->json(
            [
                'data' => $this->respondWithToken($token)->original,
                'status' => 'SUCCESS',
                'message' => 'All info for the connection.'
            ],
            201
        );
    }

    /**
     * Logout JWT
     * @param Request $request
     * @return array
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function logout(Request $request)
    {
        $auth = Auth::user();
        if ($auth) {
            // Pass true to force the token to be blacklisted "forever" 
            Auth::logout(true);
            return response()->json(
                [
                    'data' => null,
                    'status' => 'LOGOUT',
                    'message' => 'You have been successfully disconnected, and the token has been blacklisted.'
                ],
                201);
        } else {
            return response()->json(
                [
                    'data' => null,
                    'status' => 'UNAUTHORIZED',
                    'message' => 'Unauthorized'
                ],
                401);
        }
        $this->jwt->parseToken()->invalidate();
    }
}
