<?php

namespace App\Bus\Commands\Link;

use App\Link;

class EditLinkCommand
{
    /**
     * @var \App\Link
     */
    public $link;

    /**
     * @var string
     */
    public $title;

    /**
     * @var int
     */
    public $position;

    /**
     * EditLinkCommand constructor.
     * @param \App\Link $link
     * @param string    $title
     */
    public function __construct(Link $link, string $title)
    {
        $this->link = $link;
        $this->title = $title;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->link->update([
            'title' => $this->title,
        ]);
    }
}
