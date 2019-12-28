<?php

namespace App;

use App\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ReadingList extends Model
{
    use ValidationTrait, SoftDeletes;

    /**
     * @var string
     */
    const DELETED_SUCCESS_MESSAGE = "List deleted";

    /**
     * @var string
     */
    const DELETED_FAILED_MESSAGE = "List not empty";

    /**
     * @var string
     */
    const UPDATED_SUCCESS_MESSAGE = "List name updated";

    /**
     * @var string
     */
    const CREATED_SUCCESS_MESSAGE = "New list created";

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

    /**
     * @var array
     */
    public $rules = [
        'name'      => 'required|string',
        'user_id'   => 'required|integer',
    ];

    protected $casts = [
        'user_id'  => 'integer',
        'position' => 'integer',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['links'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->orderBy('position');
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getNewReadingListPosition(User $user): int
    {
        return ReadingList::where('user_id', '=', $user->id)->count() + 1;
    }

    /**
     * @param array $ids
     */
    public function reorderLists(array $ids): void
    {
        for ($i = 0; $i < sizeof($ids); $i++) {
            ReadingList::where('id', '=', $ids[$i])
                ->update([
                    'position' => $i + 1,
                ]);
        }
    }

    /**
     * @param \App\Link $link
     *
     * @return bool
     */
    public function restoreList(Link $link): bool
    {
        return (bool) $link->readingList()
            ->withTrashed()
            ->where('id', '=', $link->reading_list_id)
            ->restore();
    }

    /**
     * @return array
     */
    public function getReadingListIds(): array
    {
        return ReadingList::where('user_id', '=', Auth::user()->id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * @return bool
     */
    public function hasTrash(): bool
    {
        $links = Link::onlyTrashed()->where('reading_list_id', '=', $this->id);

        return (bool) $links->count();
    }
}
