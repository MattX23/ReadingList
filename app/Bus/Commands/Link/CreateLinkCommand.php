<?php

namespace App\Bus\Commands\Link;

use App\Link;
use Illuminate\Support\Facades\Auth;

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
        $this->position = Auth::user()->getLinkPosition($reading_list_id);
        $this->title = $title;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $link = Link::create([
            'url'             => $this->url,
            'reading_list_id' => $this->reading_list_id,
            'position'        => $this->position,
            'title'           => $this->title
        ]);

        $link->getPreview($link, $this->url);

        $link->save();
    }
}
