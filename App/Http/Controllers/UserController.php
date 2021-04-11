<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use lumilock\lumilock\App\Http\Resources\PermissionResource;
use lumilock\lumilock\App\Http\Resources\TokenResource;
use  lumilock\lumilock\App\Models\User;
use lumilock\lumilock\App\Http\Resources\UserResource;
use lumilock\lumilock\App\Models\Right;
use lumilock\lumilock\App\Models\Token;
use PhpParser\Node\Stmt\TryCatch;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            'email' => 'nullable|string|email|max:191|unique:users,email,'.Auth::id(), // unique email except for the auth id because it's
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
        if (array_key_exists('password', $filtredColumns) && array_key_exists('new_password', $filtredColumns)) {
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
     * Get all tokens from the authenticated User.
     *
     * @return Response
     */
    public function profileTokens()
    {
        // , 'client ip' => $_SERVER['REMOTE_ADDR'], 'host name' => gethostname(), 'browser' => $_SERVER['HTTP_USER_AGENT']
        return response()->json(
            [
                'data' => TokenResource::collection(Auth::user()->tokens),
                'status' => 'SUCCESS',
                'message' => 'Data of the current user.'
            ],
            200
        );
    }

    /**
     * Invalidate and remove all tokens from the authenticated User.
     *
     * @return Response
     */
    public function profileDeleteTokens()
    {
        try {
            // we get all tokens of the current user
            $tokens = Token::where('user_id', '=', Auth::id());
            // invalidate all tokens
            foreach ($tokens as $token_info) {
                JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($token_info->token), $forceForever = false);
            }
            // clean token table in the database
            $count = $tokens->delete();
            return response()->json(
                [
                    'data' => $count,
                    'status' => 'SUCCESS',
                    'message' => 'All token have been removed.'
                ],
                200
            );
        } catch (\Exception $e) {

            return response()->json(
                [
                    'data' => $e,
                    'status' => 'FAILED',
                    'message' => 'An error occured when you tried to remove tokens from a user!'
                ],
                409
            );
        }
        return response()->json(
            [
                'data' => TokenResource::collection(Auth::user()->tokens),
                'status' => 'SUCCESS',
                'message' => 'Data of the current user.'
            ],
            200
        );
    }

    /**
     * Delete one specific token from the current user.
     *
     */
    public function profileDeleteToken($tokenId)
    {
        try {
            // we select the token but verified that is a token of the current Auth
            $count = Token::where('id', $tokenId)->where('id', $tokenId)->where('user_id', Auth::id())->firstOrFail()->delete();

            return response()->json(
                [
                    'data' => $count,
                    'status' => 'SUCCESS',
                    'message' => 'The token ' . $tokenId . ' has been correctly removed.'
                ],
                200
            );
        } catch (\Exception $e) {

            return response()->json(
                [
                    'data' => null,
                    'status' => 'NOT_FOUND',
                    'message' => 'Token not found!'
                ],
                404
            );
        }
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

    /**
     * Update info of an user.
     * ! warning this function is only accessible by a super admin PERMISSIONS
     *
     * @return Response
     */
    public function updateUser(Request $request, $userId)
    {
        // we trim and remove null value from our inputs
        $inputs = array_filter(array_map('trim', $request->all()), 'strlen');

        // validate incoming request 
        $this->validate($request->merge($inputs), [
            'first_name' => 'required|regex:/^[A-Za-zÀ-ÿ\s-]+$/|max:50',
            'last_name' => 'required|regex:/^[A-Za-zÀ-ÿ\s-]+$/|max:50',
            'email' => 'nullable|string|email|max:191|unique:users',
            'password' => 'string|min:6|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/|regex:/^\S+$/|confirmed',
        ]);

        // get user we want update
        $updateUser = User::find($userId);

        // merge fillable and hidden value in an array and switch value to become keys [0 => password] -> [password => ""]
        $columns = array_fill_keys(array_merge($updateUser->getFillable(), $updateUser->getHidden()), '');
        // Columns that we can update (only fillable or hidden columns)
        $filtredColumns = array_intersect_key($inputs, $columns);

        // We check if there is old and new password
        if (array_key_exists('password', $filtredColumns)) {
            // Update the password
            $updateUser->password = app('hash')->make($filtredColumns['password']);
            $updateUser->save();
        }

        // we update profile data
        $updateUser->fill($filtredColumns);
        $updateUser->save();

        // sending a response to the user
        return response()->json(
            [
                'data' => $updateUser,
                'status' => 'SUCCESS',
                'message' => 'Data of the current user.'
            ],
            200
        );
    }
    /**
     * Delete an user.
     * ! warning this function is only accessible by a super admin PERMISSIONS
     *
     * @return Response
     */
    public function deleteUser($userId)
    {
        try {
            $count_token = Token::where('user_id', $userId)->delete();
            $count = User::findOrFail($userId)->delete();
            return response()->json(
                [
                    'data' => 'Number of users deleted : ' . $count . ' Number of token deleted : ' . $count_token,
                    'status' => 'SUCCESS',
                    'message' => 'user ' . $userId . ' has been correctly deleted!'
                ],
                201
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
    /**
     * Display all rights of a specific user.
     *
     * @param String $userId : id of the user
     * @return Response
     */
    public function rightsUser($userId)
    {
        try {
            return response()->json(
                [
                    'data' => PermissionResource::collection(Right::where('user_id', '=', $userId)->get()),
                    'status' => 'SUCCESS',
                    'message' => 'Permissions list of the user ' . $userId,
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'data' => null,
                    'status' => 'NOT_FOUND',
                    'message' => 'User not found!'
                ],
                404
            );
        }
    }
    /**
     * Display all rights of a specific user.
     *
     * @param String $userId : id of the user
     * @return Response
     */
    public function updateRightsUser(Request $request, $userId)
    {
        // validate incoming request 
        $this->validate($request, [
            'rights' => 'required|Array',
            'rights.*.id' => 'required|string|distinct|regex:/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}/|exists:permissions,id',
            'rights.*.is_active' => 'required|boolean',
        ]);
        $ids = array_column($request->input('rights'), 'id');
        $rights = array_column($request->input('rights'), 'is_active');
        $count = 0;
        foreach ($ids as $key => $id) {
            $count += Right::where('permission_id', $id)->first()->update(['is_active' => $rights[$key]]);
        }
        try {
            return response()->json(
                [
                    'data' => "Number of rights updated : $count",
                    'status' => 'SUCCESS',
                    'message' => 'Rights of user ' . $userId . ' has been updated',
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'data' => null,
                    'status' => 'NOT_FOUND',
                    'message' => 'User or Right not found!'
                ],
                404
            );
        }
    }
}
