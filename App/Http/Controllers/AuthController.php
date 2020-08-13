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
            'first_name' => 'required|regex:/^[A-Za-zÀ-ÿ\s-]+$/|max:50',
            'last_name' => 'required|regex:/^[A-Za-zÀ-ÿ\s-]+$/|max:50',
            'email' => 'nullable|string|email|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {

            $user = new User();
            $user->login = $request->input('first_name') . "\$split\$" . $request->input('last_name');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
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
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $identity  = request()->get('identity');
        $fieldName = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';
        request()->merge([$fieldName => $identity]);
        return $fieldName;
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
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        // $credentials = $request->only(['email', 'password']);

        $credentials =  array_merge($request->only($this->username(), 'password'), ['active' => true]);

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
                201
            );
        } else {
            return response()->json(
                [
                    'data' => null,
                    'status' => 'UNAUTHORIZED',
                    'message' => 'Unauthorized'
                ],
                401
            );
        }
        $this->jwt->parseToken()->invalidate();
    }
}
