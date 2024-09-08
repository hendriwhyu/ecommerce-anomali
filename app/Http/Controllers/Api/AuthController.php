<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
        } catch (ValidationException $e) {
            return $this->sendError('BadRequest', ['errors' => $e->errors()], 400);
        } catch (\Throwable $th) {
            return $this->sendError('ServerError', ['error' => $th->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validateRequest = $request->validate([
                'name' => 'required|min:4|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|required_with:confirm_password',
            ]);

            $user = User::create($validateRequest)->assignRole('panel_user');

            return $this->sendResponse([
                'user' => $user->id
            ], 'User register successfully', 201);
        }catch (ValidationException $e) {
            return $this->sendError('BadRequest', ['errors' => $e->errors()], 400);
        }catch (\Throwable $th) {
            return $this->sendError('ServerError', ['error' => $th->getMessage()], 500);
        }
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return $this->sendResponse('User Logout', 'Logout successfully');
    }

    public function showUserById($id){
        $dataUser = User::with(['roles'])->whereId($id)->first();

        if(!$dataUser){
            return $this->sendError('User not found', [], 404);
        }

        $data = new UserCollection($dataUser);

        return $this->sendResponse($data, 'User retrieved successfully');
    }
}
