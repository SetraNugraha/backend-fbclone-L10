<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $authHeader = $request->header('Authorization');

            if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
                return response()->json([
                    'success' => false,
                    'message' => 'token not provided',
                ], 401);
            }

            $token = substr($authHeader, 7); // Remove "Bearer "

            $decode = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            $user = User::find($decode->sub);

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found',
                ], 404);
            }

            // Set user to request
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'invalid or expired token',
                'error' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
