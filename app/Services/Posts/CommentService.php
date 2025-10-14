<?php

namespace App\Services\Posts;

use App\Models\Comment;

class CommentService
{
    public function create(array $payload)
    {
        return Comment::create($payload);
    }
}
