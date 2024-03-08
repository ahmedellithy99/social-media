<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PostAttachment extends Model
{
    use HasFactory;

    CONST UPDATED_AT = null;

    protected $guarded = []; 

    function post():BelongsTo
    {
        return $this->belongsTo(Post::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::deleted(function (self $model) {
            Storage::disk('public')->delete($model->path);
        });
    }
}
