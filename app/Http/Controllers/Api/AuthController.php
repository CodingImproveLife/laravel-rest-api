<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user after a valid registration.
     *
     * @param RegisterRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response([
            'user' => $user,
            'access_token' => $token
        ], 201);
    }

    /**
     * Sign in after a valid authentication.
     *
     * @param LoginRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $token = $user->createToken('authToken')->plainTextToken;

            return response([
                'user' => $user,
                'access_token' => $token
            ], 200);
        } else {
            return response([
                'message' => 'Unauthenticated.'
            ], 401);
        }
    }
}
