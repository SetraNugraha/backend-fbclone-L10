<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Posts\CommentController;
use App\Http\Controllers\Api\Posts\LikeController;
use App\Http\Controllers\Api\Posts\PostController;
use App\Http\Controllers\Api\Users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// AUTH
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('JwtAuth');
    Route::get('/refresh-token', [AuthController::class, 'refreshToken'])->middleware('JwtAuth');
});

// USER
Route::prefix('user')->middleware('JwtAuth')->group(function () {
    Route::get('/all', [UserController::class, 'getAllUsers']);
    Route::get('/{userId}', [UserController::class, 'getUserById']);
});

// POST
Route::prefix('post')->middleware('JwtAuth')->group(function () {
    Route::get('/all', [PostController::class, 'getAllPosts']);
    Route::get('/{userId}', [PostController::class, 'getUserPosts']);
    Route::post('create', [PostController::class, 'create']);

    // Comment
    Route::post('/{postId}/comment/create', [CommentController::class, 'create']);

    // Like
    Route::post('/{postId}/like/toggle', [LikeController::class, 'toggleLike']);
});
