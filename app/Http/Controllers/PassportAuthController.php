<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;

class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation error', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken("API TOKEN")->accessToken;

        return $this->respondWithSuccess('User Register Successfully', [
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($data)) {
            $userId = Auth::id();
            $accessToken = DB::table('oauth_access_tokens')->where('client_id', $userId)->first();
            if (!$accessToken) {
                $token = Auth::user()->createToken("API TOKEN")->accessToken;
                return $this->respondWithSuccess('User Logged In Successfully', [
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ]);
            }
            return $this->respondWithSuccess('User Logged In Successfully');
        } else {
            return $this->respondWithError('Invalid credentials', null, 401);
        }
    }
  

    public function sendPasswordResetToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation error', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->respondWithError('User not found', null, 404);
        }

        $token = Str::random(16);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token
        ]);

        return $this->respondWithSuccess('Copy the token and proceed with password reset', [
            'token' => $token
        ]);
    }

    public function resetPassword(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation error', $validator->errors(), 422);
        }

        $tokenData = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$tokenData) {
            return $this->respondWithError('Invalid token', null, 404);
        }

        $user = User::where('email', $tokenData->email)->first();

        if (!$user) {
            return $this->respondWithError('User not found', null, 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return $this->respondWithSuccess('Password changed successfully');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->respondWithSuccess('User Logged Out Successfully');
    }

    private function respondWithSuccess($message, $data = null, $statusCode = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    private function respondWithError($message, $errors = null, $statusCode = 400)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    public function check_login(){
        return $this->respondWithError('invalid token or login first', 401);
    }
}
