<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Api\ApiController;
use App\Services\Posts\LikeService;
use Illuminate\Http\Request;

class LikeController extends ApiController
{
    private LikeService $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    public function toggleLike(Request $request)
    {
        $user = $request->user();
        $postId = $request->postId;

        $result = $this->likeService->toggleLike($user->id, $postId);

        return $this->successResponse('toggle like success', $result['status'], 200);
    }
}
