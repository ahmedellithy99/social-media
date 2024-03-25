<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NotificationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * 
     */

    public function toArray(Request $request): array
    {
        // dd($this);
        return [
            'avatar_url' => Storage::url(User::where('id', $this->data['user_id'])->get('avatar_path')[0]['avatar_path']),
            'text' => $this->data['text'],
            'post_id' => $this->data['post_id'] ?? '',
            'username' => $this->data['username'] ?? '',
        ];
    }
}
