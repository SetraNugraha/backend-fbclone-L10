<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserPostResource;
use App\Services\Posts\PostService;
use Illuminate\Http\Request;

class PostController extends ApiController
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function getAllPosts(Request $request)
    {
        $offset = (int) $request->query('offset', 0);
        $limitPosts = (int) $request->query('limitPosts', 10);
        $limitComments = (int) $request->query('limitComments', 3);
        $data = $this->postService->getAllPosts($offset, $limitPosts, $limitComments);

        $posts = $data['posts']->map(function ($post) {
            return new PostResource($post, true);
        });

        $result = PostResource::collection($posts)->additional([
            'meta' => $data['meta'],
        ]);

        return $this->successResponse('get all posts success', $result, 200);
    }

    public function getUserPosts(Request $request)
    {
        $userId = $request->userId;
        $offset = (int) $request->query('offset', 0);
        $limitPosts = (int) $request->query('limitPosts', 10);
        $limitComments = (int) $request->query('limitComments', 3);

        $data = $this->postService->getUserPosts($userId, $offset, $limitPosts, $limitComments);

        $result = (new UserPostResource($data['posts']))->additional([
            'meta' => $data['meta'],
        ]);

        return $this->successResponse('get user posts success', $result, 200);
    }

    /**
     * @param  \App\Http\Requests\CreatePostRequest  $request
     */
    public function create(CreatePostRequest $request)
    {

        $user = $request->user();
        $validated = $request->validated();

        $post_image = null;
        if ($request->hasFile('post_image')) {
            $post_image = $request->file('post_image')->store('img/post_images', 'public');
        }

        $data = [
            'user_id' => $user->id,
            'body' => $validated['body'],
            'post_image' => $post_image,
        ];

        $result = $this->postService->create($data);

        return $this->successResponse('create post success', new PostResource($result, true), 201);
    }
}
