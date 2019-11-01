<?php

namespace App;

use App\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingList extends Model
{
    use ValidationTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
    ];

    public $rules = [
        'name'      => 'required|string',
        'user_id'   => 'required|integer',
    ];


    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
