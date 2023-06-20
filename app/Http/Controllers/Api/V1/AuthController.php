<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function masterLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }
            $user = User::where('username',$request->username)->first();
            if (!$user) {
                return responseJson('Username or Password are wrong',400,'Error');
            }

            if(! Auth::attempt($request->only('username','password'))){
                return responseJson('Username or Password are wrong',400,'Error');
            }
            $token = JWTAuth::fromUser($user);

            $data = [
                'user' => $user,
                'token' => $token,
            ];

            return responseJson('Login Successfully', 200, 'Success', $data);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function refresh()
    {
        $token = JWTAuth::getToken();

        try {
            $newToken = JWTAuth::refresh($token);
        } catch (JWTException $e) {
            return responseJson('Could not refresh token', 500, 'Error');
        }

        return response()->json(compact('newToken'));
    }
}
