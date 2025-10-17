<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Custom Logging for all exception
        $this->reportable(function (Throwable $e) {
            Log::channel('daily')->error('error: ' . $e->getMessage(), [
                'time' => now()->toDateTimeString(),
            ]);
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {

            // Route Not Found
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found',
                ], 404);
            }

            // Validation Error
            if ($exception instanceof ValidationErrorException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $exception->getErrors(),
                ], $exception->getStatusCode());
            }

            // Not Found Exception
            if ($exception instanceof NotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 404);
            }

            // Default fallback
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'internal server error',
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
