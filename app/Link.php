<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

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
        'url',
        'reading_list_id',
        'position',
    ];

    public function readingList(): BelongsTo
    {
        return $this->belongsTo(ReadingList::class);
    }

    /**
     * @param Link      $link
     * @param string    $url
     *
     * @return Link|\Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPreview(Link $link, string $url): ?Link
    {
        try {
            $targetUrl = 'https://api.linkpreview.net?key='.Config::get('linkpreview.key').'&q='.$url;

            $response = (new Client())->request('POST', $targetUrl);
            $metaData = json_decode($response->getBody());

            $link->title = $metaData->title;
            $link->description = $metaData->description;
            $link->image = $metaData->image ? $metaData->image : self::DEFAULT_IMAGE;

            return $link;
        }
        catch (\Exception $e) {
            return $link->generateDefaultMetaData($link, $url);
        }
    }

    /**
     * @param Link $link
     * @param string $url
     *
     * @return Link
     */
    protected function generateDefaultMetaData(Link $link, string $url): Link
    {
        $link->title = $url;
        $link->description = 'No description found for this link...';
        $link->image = self::DEFAULT_IMAGE;

        return $link;
    }

    /**
     * @param  int $readingList_id
     *
     * @return int
     */
    public function getNewLinkPosition(int $readingList_id) : int
    {
        return Link::where('reading_list_id', '=', $readingList_id)->count() + 1;
    }

    public function deleteInactiveList(): void
    {
        $readingList = $this->readingList()->withTrashed()->first();

        if ($readingList->links()->onlyTrashed()->count() === 1 &&
            $readingList->deleted_at !== null) $readingList->forceDelete();
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

    public function restoreSoftDeletedList(): void
    {
        $readingListIds = (new ReadingList())->getReadingListIds();

        if (!in_array($this->reading_list_id, $readingListIds)) (new ReadingList())->restoreList($this);
    }
}
