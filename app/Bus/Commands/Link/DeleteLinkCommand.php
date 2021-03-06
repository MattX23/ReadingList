<?php

namespace App\Bus\Commands\Link;

use App\Archive;
use App\Link;

class DeleteLinkCommand
{
    /**
     * @var \App\Link
     */
    public $link;

    /**
     * @var bool
     */
    public $forceDelete;

    /**
     * DeleteLinkCommand constructor.
     * @param \App\Link $link
     * @param bool      $forceDelete
     */
    public function __construct(Link $link, bool $forceDelete = false)
    {
        $this->link = $link;
        $this->forceDelete = $forceDelete;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->forceDelete ?
            $this->forceDelete() :
            $this->archive();
    }

    protected function forceDelete()
    {
        $this->deleteInactiveList();
        $this->link->forceDelete();
    }

    protected function archive()
    {
        Archive::create([
           'link_id'    => $this->link->id,
           'user_id'    => $this->link->readingList->user_id
        ]);

        $this->link->delete();
    }

    /**
     * @return void
     */
    public function deleteInactiveList(): void
    {
        $readingList = $this->link->readingList()->withTrashed()->first();

        if ($readingList->links()->onlyTrashed()->count() === 1 &&
            $readingList->deleted_at !== null) $readingList->forceDelete();
    }
}
