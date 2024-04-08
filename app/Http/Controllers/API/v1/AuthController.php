<?php

namespace App\Http\Controllers\API\v1;

use App\Actions\User\CreateUserAction;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request, CreateUserAction $createUserAction)
    {
        $userData = $request->validated();
        $userData['role'] = RoleEnum::USER;
        $user = $createUserAction->execute($userData);

        $user = new UserResource($user);
        $token = $user->createToken('authToken')->accessToken;

        return HttpResponse::success(201, 'User registered successfully', [
            'user' => $user,
            'access_token' => $token
        ]);
    }

     /**
     * Log a user in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
           return HttpResponse::error(401, 'Invalid credentials');
        }

        $user = new UserResource(auth()->user());
        $token = $user->createToken('authToken')->accessToken;

        return HttpResponse::success(200, 'Logged in successfully', [
            'user' => $user, 
            'access_token' => $token
        ]);
    }

    /**
     * Log the user out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return HttpResponse::success(200, 'Logged out successfully');
    }
}
