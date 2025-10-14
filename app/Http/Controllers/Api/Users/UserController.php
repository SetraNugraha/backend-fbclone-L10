<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Services\Users\UserService;

class UserController extends ApiController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getAllUsers()
    {
        $data = $this->userService->getAllUsers();

        return $this->successResponse('get all users success', UserResource::collection($data), 200);
    }

    public function getUserById(int $userId)
    {
        $data = $this->userService->getUserById($userId);

        return $this->successResponse('get user by id success', new UserResource($data), 200);
    }
}
