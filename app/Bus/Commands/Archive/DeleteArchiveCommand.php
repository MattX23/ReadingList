<?php

namespace App\Bus\Commands\Archive;

use App\Archive;

class DeleteArchiveCommand
{
    /**
     * @var \App\Archive
     */
    public $archive;

    /**
     * DeleteArchiveCommand constructor.
     * @param \App\Archive $archive
     */
    public function __construct(Archive $archive)
    {
        $this->archive = $archive;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->archive->delete();
    }
}
