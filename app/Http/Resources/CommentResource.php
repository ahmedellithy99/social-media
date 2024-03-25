<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(),
            'user' => [
                "id" => $this->user->id,
                "name" => $this->user->name,
                "username" => $this->user->username,
                "avatar_url" => Storage::url($this->user->avatar_path)
            ],
            'num_of_reactions' => $this->reactions_count,
            'current_user_has_reaction' => $this->reactions->count() > 0
        ];
    }
}
