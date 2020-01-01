<?php

namespace App\Bus\Commands\ReadingList;

use App\ReadingList;

class DeleteReadingListCommand
{
    /**
     * @var \App\ReadingList
     */
    public $readingList;

    /**
     * DeleteReadingListCommand constructor.
     * @param \App\ReadingList $readingList
     */
    public function __construct(ReadingList $readingList)
    {
        $this->readingList = $readingList;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->readingList->removeActiveLinks();

        $this->readingList->hasTrash() ?
            $this->readingList->delete() :
            $this->readingList->forceDelete();
    }
}
