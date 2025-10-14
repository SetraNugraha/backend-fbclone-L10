<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'author' => [
                'id' => $this->id,
                'username' => $this->first_name.' '.$this->surname,
                'email' => $this->email,
            ],
            'posts' => $this->whenLoaded('posts', function () {
                return $this->posts->map(function ($post) {
                    return new PostResource($post, false);
                });
            }),
        ];
    }
}
