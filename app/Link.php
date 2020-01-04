<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    const DEFAULT_IMAGE = '/images/icons/link-icon.png';

    /**
     * @var string
     */
    const ARCHIVED_SUCCESS_MESSAGE = 'Link archived';

    /**
     * @var string
     */
    const DELETED_SUCCESS_MESSAGE = 'Link permanently deleted';

    /**
     * @var string
     */
    const EDITED_SUCCESS_MESSAGE = 'Link title updated';

    /**
     * @var string
     */
    const RESTORED_SUCCESS_MESSAGE = 'Link restored';

    /**
     * @var string
     */
    const SAVED_SUCCESS_MESSAGE = 'Link added';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reading_list_id',
        'title',
        'url',
        'position',
    ];

    protected $appends = [
        'archive_id'
    ];

    public function readingList(): BelongsTo
    {
        return $this->belongsTo(ReadingList::class);
    }

    /**
     * @param array $ids
     */
    public function reorderLinks(array $ids)
    {
        for ($i = 0; $i < sizeof($ids); $i++) {
            Link::where('id', '=', $ids[$i])
                ->update([
                    'position' => $i + 1,
                ]);
        }
    }

    /**
     * @return int|null
     */
    public function getArchiveIdAttribute(): ?int
    {
        return $this->trashed() ? Archive::where('link_id', '=', $this->id)->first()->id : null;
    }
}
