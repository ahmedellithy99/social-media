<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    use HasFactory;

    const UPDATED_AT = NULL;
    const CREATED_AT = NULL;

    protected $guarded = [];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function userA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'A');
    }

    public function userB(): BelongsTo
    {
        return $this->belongsTo(User::class, 'B');
    }
}
