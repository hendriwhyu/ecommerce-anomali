<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
            if(Auth::attempt($credentials)){
                $user = Auth::user();
                $success['token'] =  $user->createToken('Anomali')->plainTextToken;
                $success['name'] =  $user->name;

                return $this->sendResponse($success, 'User login successfully.');
            }
            else{
                return $this->sendError('Unauthorised', ['error' => 'Unauthorised'], 401);
            }
        } catch (\Throwable $th) {
            return $this->sendError('ServerError', ['error' => $th->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validateRequest = $request->validate([
                'name' => 'required|min:4',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = User::create($validateRequest)->roles;
            $user->assignRole('panel_user');
            $success['token'] =  $user->createToken('Anomali')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User register successfully', 201);
        } catch (\Throwable $th) {
            return $this->sendError('ServerError', ['error' => $th->getMessage()], 500);
        }
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return $this->sendResponse('User Logout', 'Logout successfully');
    }
}
