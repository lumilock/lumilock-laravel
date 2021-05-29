<?php

namespace lumilock\lumilock\App\Http\Controllers;

use Carbon\Carbon;
use DateInterval;
use lumilock\lumilock\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

//import auth facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use lumilock\lumilock\App\Http\Resources\UserResource;
use lumilock\lumilock\App\Models\Token;
use lumilock\lumilock\App\Models\User;
use lumilock\lumilock\Facades\lumilock as FacadesLumilock;
use lumilock\lumilock\Lumilock;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['check']]);
    }

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
            'password' => 'required|string|min:6|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/|regex:/^\S+$/|confirmed',
        ]);

        try {
            // Save user in DB
            $user = new User();
            $user->login = $request->input('first_name') . "\$split\$" . $request->input('last_name');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->picture = time() . '.' . uniqid() . '.png'; // generate a fileName
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            // Generate User profile picture
            $img_size = 512;
            $img = imagecreate($img_size, $img_size);

            // generate random color for background
            $red = mt_rand(50, 150);
            $green = mt_rand(50, 150);
            $blue = mt_rand(50, 150);
            imagecolorallocate($img, $red, $green, $blue);
            $font_color = imagecolorallocate($img, 255, 255, 255);
            $font = __DIR__.'/../../../Public/Roboto-Regular.ttf';

            // Generate initials text
            $initials = FacadesLumilock::generate("$user->first_name $user->last_name");
            $font_size  = 128;
            $f_width = 110;
            $f_heigth = -124;
            $font_width = $f_width * strlen($initials);
            $font_height = $f_heigth;

            imagettftext($img, $font_size, 0, ($img_size/2)-($font_width/2), ($img_size/2)-($font_height/2), $font_color , $font, $initials);

            // Convert image to store it
            $image = Image::make($img);
            // Set image name
            $fileName   = $user->picture;
            $image->stream(); // <-- Key point
            // Store image
            Storage::disk('local')->put("Users/$user->id/profile/" . $fileName, $image, 'public');
            // Remove image id generation
            imagedestroy($img);

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
        try {
            $identity  = request()->get('identity');
            $fieldName = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';
            request()->merge([$fieldName => $identity]);
            return $fieldName;
        } catch (\Exception $e) {
            dd('username error :', $e);
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
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $credentials = array_merge($request->only($this->username(), 'password'), ['active' => true]);

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
            $user = Auth::user();
            $token_info = $this->respondWithToken($token)->original;

            // We calculate the token expiration date from the $token_info->expires_in value
            // $time = Carbon::now(); // Date time now
            // $time->add(new DateInterval('PT' . $token_info['expires_in'] . 'S')); // We add the duration that left to our token
            // $stamp = $time->format('Y-m-d H:i'); // Format conversion

            $stamp = Carbon::now()->add(new DateInterval('PT' . $token_info['expires_in'] . 'S'));
            // we remove all expires tokens
            Token::where('expires_at', '<=', Carbon::now())->delete();

            // we create a token in database
            $tokeModel = new Token();
            $tokeModel->user_id = $user->id;
            $tokeModel->expires_at = $stamp;
            $tokeModel->token = $token_info['token'];
            $tokeModel->save();


            // response to the user by giving him token_info and user data 
            return response()->json(
                [
                    'data' => compact('token_info', 'user'),
                    'status' => 'SUCCESS',
                    'message' => 'All info for the connection.'
                ],
                201
            );
        } catch (\Exception $e) {
            dd("Login error : " . $e);
        }
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


    /**
     * Get authenticated user
     */
    public function check(Request $request)
    {
        try {
            // get the value of the token
            $token_value = JWTAuth::getToken()->get();
            // get the user like to the token (so auth user)
            $user = new UserResource(JWTAuth::user());
            // decode the token in order to get the expiration time
            $decode_token = JWTAuth::getPayload($token_value)->toArray();

            // create the response
            $token_info = [
                'token' => $token_value,
                'expires_in' => $decode_token['exp'] - time(), // get in seconde time before expiration
                'token_type' => "bearer"
            ];

            return response()->json([
                'data' => compact('token_info', 'user'),
                'status' => 'USER_VERIFIED',
                'message' => 'Unauthorized'
            ], 200);
        } catch (\Exception $e) {
            dd('check error : ' . $e);
        }
    }
}
