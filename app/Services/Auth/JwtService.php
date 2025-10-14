<?php

namespace App\Services\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    protected string $secret;

    protected int $accessExpire;

    protected int $refreshExpire;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
        $this->accessExpire = env('JWT_ACCESS_EXPIRE', 1200);
        $this->refreshExpire = env('JWT_REFRESH_EXPIRE', 86400);
    }

    public function generateToken($user)
    {
        $now = time();

        $payloadAccess = [
            'sub' => $user->id,
            'type' => 'access',
            'iat' => $now,
            'exp' => $now + $this->accessExpire,
        ];

        $payloadRefresh = [
            'sub' => $user->id,
            'type' => 'refresh',
            'iat' => $now,
            'exp' => $now + $this->refreshExpire,
        ];

        $accessToken = JWT::encode($payloadAccess, $this->secret, 'HS256');
        $refreshToken = JWT::encode($payloadRefresh, $this->secret, 'HS256');

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    public function verifyToken($token)
    {
        try {
            return JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (\Throwable $th) {
            return null;
        }
    }
}
