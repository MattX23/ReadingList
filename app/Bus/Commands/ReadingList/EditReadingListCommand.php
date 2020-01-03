<?php

namespace App\Bus\Commands\ReadingList;

use App\ReadingList;

class EditReadingListCommand
{
    /**
     * @var \App\ReadingList
     */
    public $readingList;

    /**
     * @var string
     */
    public $name;

    /**
     * EditReadingListCommand constructor.
     * @param \App\ReadingList $readingList
     * @param string           $name
     */
    public function __construct(ReadingList $readingList, string $name)
    {
        $this->readingList = $readingList;
        $this->name = $name;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->readingList->update([
            'name' => $this->name
        ]);
    }
}
