<?php

namespace App\Services\Posts;

use App\Models\Post;
use App\Models\User;

class PostService
{
    public function getAllPosts(int $offset = 0, int $limitPosts = 10, int $limitComments = 5)
    {
        $posts = Post::with([
            'user',
            'likes',
            'comments' => function ($query) {
                $query->with('user')->latest();
            },
        ])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->skip($offset)
            ->take($limitPosts)
            ->get();

        $totalPosts = Post::count();

        $posts->each(function ($post) use ($limitComments) {
            $post->setRelation('comments', $post->comments->take($limitComments));
        });

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
            'posts' => function ($query) use ($offset, $limitPosts) {
                $query->withCount(['likes', 'comments'])
                    ->latest()
                    ->skip($offset)
                    ->take($limitPosts)
                    ->with([
                        'comments' => function ($queryComments) {
                            $queryComments->with('user')->latest();
                        },
                        'likes',
                    ]);
            },
        ])->findOrFail($userId);

        $totalPosts = User::findOrFail($userId)->posts()->count();

        $userPosts->posts->each(function ($post) use ($limitComments) {
            $post->setRelation('comments', $post->comments->take($limitComments));
        });

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
