<?php

namespace App;

use App\Traits\ValidationTrait;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Link extends Model
{
    use ValidationTrait, SoftDeletes;

    /**
     * @var string
     */
    const DEFAULT_IMAGE = '/images/icons/link-icon.png';

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

    /**
     * @var array
     */
    public $rules = [
        'url'               => 'required|url',
        'reading_list_id'   => 'required|integer',
        'title'             => 'required|string',
    ];

    public function readingList(): BelongsTo
    {
        return $this->belongsTo(ReadingList::class);
    }

    /**
     * @param Link      $link
     * @param string    $url
     *
     * @return          Link|\Illuminate\Http\JsonResponse
     * @throws          \GuzzleHttp\Exception\GuzzleException
     */
    public function getPreview(Link $link, string $url): ?Link
    {
        try {
            $client = new Client();
            $targetUrl = 'https://api.linkpreview.net?key='.Config::get('linkpreview.key').'&q='.$url;

            $response = $client->request('POST', $targetUrl);
            $metaData = json_decode($response->getBody());

            $link->title = $metaData->title;
            $link->description = $metaData->description;
            $link->image = $metaData->image ? $metaData->image : self::DEFAULT_IMAGE;

            return $link;
        }
        catch (\Exception $e) {
            return null;
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
     * @param int           $readingList_id
     *
     * @return              int
     */
    public function getNewLinkPosition(int $readingList_id) : int
    {
        return Link::where('reading_list_id', '=', $readingList_id)->count() + 1;
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
}
