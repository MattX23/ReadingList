<?php

namespace App\Bus\Commands\Link;

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
        $this->forceDelete ? $this->forceDelete() : $this->link->delete();
    }

    protected function forceDelete()
    {
        $this->link->deleteInactiveList();
        $this->link->forceDelete();
    }
}
