<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ReadingList extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    const DELETED_SUCCESS_MESSAGE = "List deleted";

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

    public static function boot()
    {
        parent::boot();

        self::deleted(function () {
            $lists = ReadingList::all()->sortBy('position');

            $i = 1;

            foreach ($lists as $list) {
                $list->update([
                    'position' => $i
                ]);
                $i++;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->orderBy('position');
    }

    /**
     * @return array
     */
    public function getIds(): array
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

    /**
     * @param int[] $ids
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

    public function removeActiveLinks()
    {
        $this->links()->each(function(Link $link) {
            $link->forceDelete();
        });
    }

    /**
     * @return int
     */
    public function getLinkPosition(): int
    {
        return Link::where('reading_list_id', '=', $this->id)->count() + 1;
    }
}
