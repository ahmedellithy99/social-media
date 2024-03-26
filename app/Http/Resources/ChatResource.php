<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this->lastMessage->read);
        return [
            'id' => $this->id,
            'subId' => $this->subId,
            'user' => $this->userA->id == auth()->user()->id ? new UserResource($this->userB) : new UserResource($this->userA),
            'lastMessage' => $this->lastMessage,
            'timeOflastMessage' => $this->lastMessage ? Carbon::parse($this->lastMessage->created_at)->diffForHumans() : null,
            'is_read' => $this->lastMessage->read ?? 0
        ];
    }
}
