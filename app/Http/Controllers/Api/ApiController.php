<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiController extends Controller
{
    public function successResponse(string $message = 'success', $data = null, int $statusCode = 200): JsonResponse
    {
        // If add additional() with Resource
        if ($data instanceof JsonResource) {
            $resourceResponse = $data->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $resourceResponse['data'] ?? $resourceResponse,
                'meta' => $resourceResponse['meta'] ?? null,
            ], $statusCode);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function errorResponse(string $message = 'error', array $data = [], int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, [], 404);
    }

    protected function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return $this->errorResponse($message, [], 500);
    }
}
