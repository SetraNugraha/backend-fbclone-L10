<?php

namespace App\Services\Users;

use App\Exceptions\NotFoundException;
use App\Models\User;

class UserService
{
    public function getAllUsers()
    {
        $data = User::all();

        return $data;
    }

    public function getUserById(int $userId)
    {
        $data = User::find($userId);

        if (! $data) {
            throw new NotFoundException('user not found');
        }

        return $data;
    }
}
