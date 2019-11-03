<?php

namespace App;

use App\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use ValidationTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'reading_list_id',
    ];

    public $rules = [
        'url'               => 'required|url',
        'reading_list_id'   => 'required|integer',
    ];

    public function readingList() : BelongsTo
    {
        return $this->belongsTo(ReadingList::class);
    }
}
