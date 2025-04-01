<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{

    public function register(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $validated = $validator->validated();

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $token = $newUser->createToken('auth_token')->plainTextToken;

        $result = [
            'token' => $token,
            'user' => $newUser,
        ];
        return $this->sendResponse($result, 'User register successfully.', 201);
    }

    public function login(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        if(Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            $user = Auth::user();
            $token = $user?->createToken('auth_token')->plainTextToken;
            $result = [
                'token' => $token,
                'user' => $user,
            ];
            return $this->sendResponse($result, 'User login successfully.', 200);
        }
        return $this->sendError('Unauthorised.', null,401);
    }


    public function logout(Request $request): JsonResponse {
        if(Auth::user()) {
             Auth::user()->tokens()->delete();
            return $this->sendResponse(null, 'User logout successfully.');
        }

        return $this->sendError('Unauthorised.',null , 401);
    }

    public function profile(Request $request): JsonResponse {
        $user = Auth::user();
        return $this->sendResponse($user, 'User profile successfully.', 200);
    }
}
