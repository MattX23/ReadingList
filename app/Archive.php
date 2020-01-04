<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archive extends Model
{
    protected $fillable = [
        'link_id',
        'user_id',
    ];

    protected $casts = [
        'link_id'  => 'integer',
        'user_id'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
