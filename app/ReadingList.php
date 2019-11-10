<?php

namespace App;

use App\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'position',
    ];

    public $rules = [
        'name'      => 'required|string',
        'user_id'   => 'required|integer',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['links'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links() : HasMany
    {
        return $this->hasMany(Link::class)->orderBy('position');
    }

    /**
     * @param User $user
     * @return int
     */
    public function getNewReadingListPosition(User $user) : int
    {
        return ReadingList::where('user_id', '=', $user->id)->count() + 1;
    }

    /**
     * @param array $ids
     */
    public function reorderLists(array $ids)
    {
        for ($i = 0; $i < sizeof($ids); $i++) {
            ReadingList::where('id', '=', $ids[$i])
                ->update([
                    'position' => $i + 1,
                ]);
        }
    }
}
