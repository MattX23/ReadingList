<?php

namespace App;

use App\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReadingList extends Model
{
    use ValidationTrait;

    const RESTORED_LIST = 'Restored Links';

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

    protected $casts = [
        'user_id' => 'integer'
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
     * @return ReadingList
     */
    public function createRestoredLinksList(): ReadingList
    {
        $readingList = new ReadingList([
            'name'     => self::RESTORED_LIST,
            'user_id'  => Auth::user()->id,
            'position' => count(Auth::user()->readingLists) + 1
        ]);

        $readingList->save();

        return $readingList;
    }

    /**
     * @return array
     */
    public function getReadingListIds(): array
    {
        return DB::table('reading_lists')
            ->where('user_id', '=', Auth::user()->id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * @param Link $link
     * @param int $id
     */
    public function updateReadingList(Link $link, int $id): void
    {
        $link->update([
            'reading_list_id' => $id
        ]);
    }
}
