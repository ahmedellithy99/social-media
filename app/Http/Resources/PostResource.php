<?php

namespace App\Http\Resources;

use App\Models\Reaction;
use Carbon\Carbon;
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
        
        // dd(isset($this->reactions));
        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),
            'user' => new UserResource($this->user),
            'group' => $this->group,
            'attachments' => PostAttachmentsResource::collection($this->attachments),
            'num_of_reactions' => $this->reactions_count,
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'num_of_comments' => $this->comments_count,
            'comments' => CommentResource::collection($this->comments)
        ];
    }
}
