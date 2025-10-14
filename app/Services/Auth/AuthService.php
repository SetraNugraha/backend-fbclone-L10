<?php

namespace App\Services\Auth;

use App\Exceptions\ValidationErrorException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function register(array $payload)
    {
        $payload['password'] = Hash::make($payload['password']);
        $payload['birthday'] = "{$payload['date']}-{$payload['month']}-{$payload['year']}";
        unset($payload['date'], $payload['month'], $payload['year']);

        return User::create($payload);
    }

    public function login(array $request)
    {
        $user = User::where('email', $request['email'])->first();

        if (! $user) {
            throw new ValidationErrorException(['email' => ['email not registered yet']]);
        }

        if (! Hash::check($request['password'], $user->password)) {
            throw new ValidationErrorException(['password' => ['incorrect password']]);
        }

        $tokens = $this->jwtService->generateToken($user);

        // save refresh token on DB user
        $user->update([
            'refresh_token' => $tokens['refresh_token'],
        ]);

        return [
            'user' => $user,
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ];
    }

    public function logout(User $user)
    {
        $user->update([
            'refresh_token' => null,
        ]);

        return [
            'success' => true,
            'message' => 'logout success',
        ];
    }

    public function refreshToken(string $refreshToken)
    {
        if (! $refreshToken) {
            throw new \Exception('token not provided', 401);
        }

        $decode = $this->jwtService->verifyToken($refreshToken);
        if (! $decode) {
            throw new \Exception('invalid or expired token', 401);
        }

        $user = User::find($decode->sub);
        if (! $user) {
            throw new \Exception('user not found', 404);
        }

        // Generete new tokens
        $tokens = $this->jwtService->generateToken($user);
        $user->update(['refresh_token' => $tokens['refresh_token']]);

        return [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ];
    }
}
