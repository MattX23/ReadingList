<?php

namespace App\Bus\Commands\Link;

use App\Link;
use App\ReadingList;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class CreateLinkCommand
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $reading_list_id;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $title;

    public function __construct(string $url, int $reading_list_id, string $title)
    {
        $this->url = $url;
        $this->reading_list_id = $reading_list_id;

        $readingList = ReadingList::find($reading_list_id)->first();

        $this->position = $readingList->getLinkPosition();
        $this->title = $title;
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $link = Link::create([
            'url'             => $this->url,
            'reading_list_id' => $this->reading_list_id,
            'position'        => $this->position,
            'title'           => $this->title
        ]);

        $this->getPreview($link)
            ->save();
    }

    /**
     * @param Link $link
     *
     * @return Link|\Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPreview(Link $link): ?Link
    {
        try {
            $targetUrl = 'https://api.linkpreview.net?key='.Config::get('linkpreview.key').'&q='.$this->url;

            $response = (new Client())->request('POST', $targetUrl);
            $metaData = json_decode($response->getBody());

            $link->title = $metaData->title;
            $link->description = $metaData->description;
            $link->image = $metaData->image ?: Link::DEFAULT_IMAGE;

            return $link;
        }
        catch (\Exception $e) {
            return $this->generateDefaultMetaData($link);
        }
    }

    /**
     * @param \App\Link $link
     *
     * @return Link
     */
    protected function generateDefaultMetaData(Link $link): Link
    {
        $link->title = $this->url;
        $link->description = 'No description found for this link...';
        $link->image = Link::DEFAULT_IMAGE;

        return $link;
    }
}
