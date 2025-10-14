<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\FormLoginRequest;
use App\Http\Requests\Auth\FormRegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(FormRegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse('register success', new UserResource($result), 201);
    }

    public function login(FormLoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        $response = response()->json([
            'success' => true,
            'message' => 'login success',
            'data' => [
                'user' => $result['user'],
                'access_token' => $result['access_token'],
            ],
        ], 200);

        $response->cookie(
            'refresh_token',
            $result['refresh_token'],
            60 * 24, // 1 Day
            '/',
            null,
            false, // secure (HTTPS)
            true, // httpOnly
            false, // raw
            'Strict' // sameSite
        );

        return $response;
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $result = $this->authService->logout($user);
        $response = response()->json($result);
        $response->cookie('refresh_token', '', -1);

        return $response;
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');
        $result = $this->authService->refreshToken(($refreshToken));
        $response = response()->json([
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
        ], 200);

        $response->cookie(
            'refresh_token',
            $result['refresh_token'],
            60 * 24, // 1 Day
            '/',
            null,
            false, // secure (HTTPS)
            true, // httpOnly
            false, // raw
            'Strict' // sameSite
        );

        return $response;
    }
}
