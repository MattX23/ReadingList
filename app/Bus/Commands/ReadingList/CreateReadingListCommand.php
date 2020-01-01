<?php

namespace App\Bus\Commands\ReadingList;

use App\ReadingList;
use App\User;

class CreateReadingListCommand
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var int
     */
    public $position;

    /**
     * CreateReadingListCommand constructor.
     * @param string    $name
     * @param \App\User $user
     */
    public function __construct(string $name, User $user)
    {
        $this->name = $name;
        $this->user_id = $user->id;
        $this->position = $user->getReadingListPosition();
    }

    /**
     * @return void
     */
    public function handle()
    {
        ReadingList::create([
            'name'      => $this->name,
            'user_id'   => $this->user_id,
            'position'  => $this->position
        ]);
    }
}
