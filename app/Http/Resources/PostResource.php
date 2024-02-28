<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'body' => $this->body,
            'created_at' => $this->created_at->format('y-m-d'),
            'updated_at' => $this->updated_at->format('y-m-d'),
            'user' => new UserResource($this->user),
            'group' => $this->group,
            'attachments' => $this->attachments
        ];
    }
}
