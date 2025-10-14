<?php

namespace App\Services\Posts;

use App\Models\Post;
use App\Models\User;

class PostService
{
    public function getAllPosts(int $offset = 0, int $limitPosts = 10, int $limitComments = 3)
    {
        $posts = Post::with([
            'user',
            'likes',
            'comments' => function ($query) use ($limitComments) {
                $query->latest()->limit($limitComments);
            },
        ])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->skip($offset)
            ->take($limitPosts)
            ->get();

        $totalPosts = Post::count();

        return [
            'posts' => $posts,
            'meta' => [
                'offset' => $offset,
                'limitPosts' => $limitPosts,
                'limitComments' => $limitComments,
                'totalPost' => $totalPosts,
                'hasMore' => ($offset + $limitPosts) < $totalPosts,
            ],
        ];
    }

    public function getUserPosts(int $userId, int $offset = 0, int $limitPosts = 10, int $limitComments = 3)
    {
        $userPosts = User::with([
            'posts' => function ($query) use ($offset, $limitPosts, $limitComments) {
                $query->withCount(['likes', 'comments'])
                    ->latest()
                    ->skip($offset)
                    ->take($limitPosts)
                    ->with([
                        'comments' => function ($queryComments) use ($limitComments) {
                            $queryComments->latest()->limit($limitComments);
                        },
                        'likes',
                    ]);
            },
        ])->findOrFail($userId);

        $totalPosts = $userPosts->posts->count();

        return [
            'posts' => $userPosts,
            'meta' => [
                'offset' => $offset,
                'limitPosts' => $limitPosts,
                'limitComments' => $limitComments,
                'totalPost' => $totalPosts,
                'hasMore' => ($offset + $limitPosts) < $totalPosts,
            ],
        ];
    }

    public function create(array $payload)
    {
        return Post::create($payload);
    }
}
