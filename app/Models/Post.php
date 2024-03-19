<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group():BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attachments():HasMany
    {
        return $this->hasMany(PostAttachment::class)->latest();
    }
    
    public function reactions():MorphMany
    {
        return $this->morphMany(Reaction::class , 'reactable');
    }

    public function comments():HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function scopeItems(Builder $query , $userId):void
    {
        $query->with('attachments')->withCount('reactions')
        ->withCount('comments')
        ->with(['user','comments' => function($query) use ($userId) {
            $query->withCount('reactions')->with(
                ['user','reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
        ]);
        }
        ,'reactions' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }]);
    }
}
