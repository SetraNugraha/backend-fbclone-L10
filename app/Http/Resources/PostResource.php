<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    private bool $withUser;

    public function __construct($resource, bool $withUser = true)
    {
        parent::__construct($resource);
        $this->withUser = $withUser;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'body' => $this->body,
            'post_image' => $this->post_image,
            'created_at' => $this->created_at,
            'likes_count' => $this->when(isset($this->likes_count), $this->likes_count),
            'comments_count' => $this->when(isset($this->comments_count), $this->comments_count),
            'likes' => $this->whenLoaded('likes', function () {
                return $this->likes->map(function ($like) {
                    return [
                        'id' => $like->id,
                        'user_id' => $like->user_id,
                    ];
                });
            }),
            'comments' => $this->whenLoaded('comments', function () {
                return $this->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'body' => $comment->body,
                        'created_at' => $comment->created_at,
                        'user' => [
                            'id' => $comment->user->id,
                            'username' => $comment->user->first_name . ' ' . $comment->user->surname,
                            'profile_image' => $comment->user->profile_image
                                ? asset('storage/' . $comment->user->profile_image) : null,
                        ],
                    ];
                });
            }),

        ];

        if ($this->withUser) {
            $data['author'] = $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'username' => $this->user->first_name . ' ' . $this->user->surname,
                    'email' => $this->user->email,
                    'profile_image' => $this->user->profile_image
                        ? asset('storage/' . $this->user->profile_image)
                        : null,
                ];
            });
        }

        return $data;
    }
}
