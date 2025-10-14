<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Posts\CreateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Services\Posts\CommentService;

class CommentController extends ApiController
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @param  \App\Http\Requests\CreateCommentRequest  $request
     */
    public function create(CreateCommentRequest $request)
    {
        $user = $request->user();
        $postId = $request->postId;
        $validated = $request->validated();

        $data = [
            'user_id' => $user->id,
            'post_id' => $postId,
            'body' => $validated['body'],
        ];

        $result = $this->commentService->create($data);

        return $this->successResponse('create comment success', new CommentResource($result), 201);
    }
}
