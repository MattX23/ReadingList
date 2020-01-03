<?php

namespace App\Bus\Commands\Link;

use App\Link;
use App\ReadingList;

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
        $this->restoreSoftDeletedList();
        $this->link->restore();
    }

    public function restoreSoftDeletedList(): void
    {
        $readingListIds = $this->link->readingList->getIds();

        if (!in_array($this->link->reading_list_id, $readingListIds)) $this->restoreDormantList();
    }

    /**
     * @return bool
     */
    public function restoreDormantList(): bool
    {
        return (bool) $this->link->readingList()
            ->withTrashed()
            ->where('id', '=', $this->link->reading_list_id)
            ->restore();
    }
}
