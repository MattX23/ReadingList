<?php

namespace App;

use App\Traits\ValidationTrait;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;

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


    /**
     * @param Link $link
     * @param string $url
     * @return Link|\Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
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
}
