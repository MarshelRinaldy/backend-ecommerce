<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{   

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(),'Data profile user berhasil diambil');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ],'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();
            if ( ! Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Authentication Failed', 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['nullable', 'string', 'max:255'],
                'onesignal_player_id' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string', new Password]
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,
                'onesignal_player_id' => $request->onesignal_player_id,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Authentication Failed', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token,'Token Revoked');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user,'Profile Updated');
    }
    // public function register(Request $request){
    //     try{
    //     $request->validate([
    //         'name' => ['required', 'string','max:255'],
    //         'username' => ['required', 'string', 'max:255','unique:users'],
    //         'email' => ['required', 'string', 'email', 'max:255','unique:users'],
    //         'phone' => ['nullable', 'string', 'max:255'],
    //         'password' => ['required', 'string', new Password],
    //         //intinya pada password tersebut menggunakan rules yang sudah disediakan laravel agar lebih aman
    //     ]);

    //     User::create([
    //         'name' => $request->name,
    //         'username' => $request->username,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     $tokenResult = $user->createToken('authToken')->plainTextToken;

    //         return ResponseFormatter::success([
    //             'access_token' => $tokenResult,
    //             'token_type' => 'Bearer',
    //             'user' => $user,
    //         ], 'User Registered');

    //     } catch(Exception $error) {
    //         return ResponseFormatter::error([
    //             'message' => 'Something went wrong',
    //             'error' => $error,
    //         ], 'Authentication Failed', 500); 
    //     }          
    // }

    // public function login(Request $request){
    //     try {
    //         $request->validate([
    //             'email' => 'email|required',
    //             'password' => 'required',
    //         ]);

    //         $credientials = request(['email', 'password']);

    //         if(!Auth::attempt($credientials)){
    //             return ResponseFormatter::error([
    //                 'message' => 'Unauthorized'
    //             ], 'Authentication Failed', 500);
    //         }

    //         $user = User::where('email', $request->email)->first();
            
    //         if(! Hash::check($request->password, $user->password, [])){
    //             throw new \Exception('Invalid Credential');
    //         }
            
    //         $tokenResult = $user->createToken('authToken')->plainTextToken;

    //         return ResponseFormatter::success([
    //             'access_token' => $tokenResult,
    //             'token_type' => 'Bearer',
    //             'user' => $user,
    //         ], 'Aunthenticated');
            
    //     } catch (Exception $error) {
    //         return ResponseFormatter::error([
    //                 'message' => 'Something went wrong',
    //                 'error' => $error,
    //             ], 'Authentication Failed', 500);
    //     }
    // }
    // public function fetch(Request $request){
    //     return ResponseFormatter::success($request->user(), 'Data Profile User Berhasil Diambil');
    // }

    // public function updateProfile(Request $request){
    //     $data = $request->all();

    //     $request->validate([
    //         'name' => ['required', 'string','max:255'],
    //         'username' => ['required', 'string', 'max:255','unique:users'],
    //         'email' => ['required', 'string', 'email', 'max:255','unique:users'],
    //         'phone' => ['nullable', 'string', 'max:255'],
    //         'password' => ['required', 'string', new Password],
    //         //intinya pada password tersebut menggunakan rules yang sudah disediakan laravel agar lebih aman
    //     ]);

    //     $user = Auth::user();
    //     $user->update($data);
    //     return ResponseFormatter::success($user, 'Profile Update');
    // }

    // public function logout(Request $request){
    //     $token = $request->user()->currentAccessToken()->delete(); 

    //     return ResponseFormatter::success($token, 'Token Revoked');
    // }
}
