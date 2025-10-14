<?php

namespace App\Services\Posts;

use App\Models\Like;

class LikeService
{
    public function toggleLike(int $userId, int $postId)
    {
        $hasLiked = Like::where(['user_id' => $userId, 'post_id' => $postId])->first();

        if ($hasLiked) {
            $hasLiked->delete();

            return [
                'status' => 'unlike',
            ];
        }

        Like::create(['user_id' => $userId, 'post_id' => $postId]);

        return [
            'status' => 'liked',
        ];
    }
}
