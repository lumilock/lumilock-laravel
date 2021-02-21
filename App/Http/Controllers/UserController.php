<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Illuminate\Http\Request;
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
     * Update info of the authenticated User.
     *
     * @return Response
     */
    public function updateProfile(Request $request)
    {
        // we trim and remove null value from our inputs
        $inputs = array_filter(array_map('trim', $request->all()), 'strlen');

        // validate incoming request 
        $this->validate($request->merge($inputs), [
            'first_name' => 'required|regex:/^[A-Za-zÀ-ÿ\s-]+$/|max:50',
            'last_name' => 'required|regex:/^[A-Za-zÀ-ÿ\s-]+$/|max:50',
            'email' => 'nullable|string|email|max:191|unique:users',
            'new_password' => 'string|min:6|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/|regex:/^\S+$/|confirmed|different:password',
            'password' => 'required_with:new_password|string',
        ]);

        // get user we want update
        $updateProfile = User::find(Auth::id());

        // merge fillable and hidden value in an array and switch value to become keys [0 => password] -> [password => ""]
        $columns = array_fill_keys(array_merge($updateProfile->getFillable(), $updateProfile->getHidden(), ['new_password']), '');
        // Columns that we can update (only fillable or hidden columns)
        $filtredColumns = array_intersect_key($inputs, $columns);

        // we check if there is old and new password
        if (array_key_exists('password', $filtredColumns) && array_key_exists('new_password', $filtredColumns) ) {
            // We check that the old password is correct
            if (app('hash')->check($filtredColumns['password'], $updateProfile->password)) {
                $updateProfile->password = app('hash')->make($filtredColumns['new_password']);
                $updateProfile->save();
            } else {
                // sending an error response to the user
                return response()->json(
                    [
                        'data' => null,
                        'status' => 'FAILED',
                        'message' => 'The modification of your password failed, check that your old password is correct.'
                    ],
                    409
                );
            }
        }

        // we update profile data
        $updateProfile->fill($filtredColumns);
        $updateProfile->save();

        // sending a response to the user
        return response()->json(
            [
                'data' => $updateProfile,
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
