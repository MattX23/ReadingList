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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'reading_list_id',
        'position',
    ];

    public $rules = [
        'url'               => 'required|url',
        'reading_list_id'   => 'required|integer',
        'title'             => 'required|string',
    ];

    public function readingList() : BelongsTo
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
    public function getPreview(Link $link, string $url) : ?Link
    {
        try {
            $client = new Client();
            $targetUrl = 'https://api.linkpreview.net?key='.Config::get('linkpreview.key').'&q='.$url;

            $response = $client->request('POST', $targetUrl);
            $metaData = json_decode($response->getBody());

            $link->title = $metaData->title;
            $link->description = $metaData->description;
            $link->image = $metaData->image;

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
    public function generateDefaultMetaData(Link $link, string $url) : Link
    {
        $link->title = $url;
        $link->description = 'No description found for this link...';
        $link->image = '/images/icons/link-icon.png';

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

    /**
     * @param Link $link
     *
     * @param int $oldListId
     */
    public function redefinePositions(Link $link, int $oldListId)
    {
        $oldLinks = Link::where('reading_list_id', '=', $oldListId)
                        ->orderBy('position')
                        ->get();

        $position = 1;

        foreach ($oldLinks as $oldLink) {
            $oldLink->update([
                'position' => $position,
            ]);
            $position++;
        }

        $newPosition = count(ReadingList::find($link->reading_list_id)->links);

        $link->update([
            'position' => $newPosition,
        ]);
    }
}
