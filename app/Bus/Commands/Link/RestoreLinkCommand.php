<?php

namespace App\Bus\Commands\Link;

use App\Link;

class RestoreLinkCommand
{
    /**
     * @var \App\Link
     */
    public $link;

    /**
     * RestoreLinkCommand constructor.
     * @param \App\Link $link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->link->restoreSoftDeletedList();
        $this->link->restore();
    }
}
