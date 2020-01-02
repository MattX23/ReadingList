<?php

namespace Tests\Traits;

use App\Link;
use App\ReadingList;
use App\User;

trait SetUpTrait {

    /**
     * @var \App\User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @param int       $listCount
     * @param int|null  $linkCount
     *
     * @return void
     */
    public function addListsAndLinks(int $listCount, int $linkCount = null): void
    {
        $this->addReadingLists($listCount);

        $this->user->readingLists->each(function ($readingList) use ($linkCount) {
            factory(Link::class, $linkCount)->create([
                'reading_list_id' => $readingList->id,
            ]);
        });

        $this->incrementPositions();
    }

    /**
     * @param int $listCount
     *
     * @return void
     */
    protected function addReadingLists(int $listCount): void
    {
        $this->user->each(function ($user) use ($listCount) {
            factory(ReadingList::class, $listCount)->create([
                'user_id' => $user->id,
            ]);
        });
    }

    /**
     * @return void
     */
    protected function incrementPositions(): void
    {
        $i = 1;
        $this->user->readingLists->each(function($readingList) use (&$i) {
            $readingList->update([
                'position' => $i
            ]);
            $i++;

            $c = 1;
            foreach ($readingList->links as $link) {
                $link->update([
                    'position' => $c,
                ]);
                $c++;
            }
        });
    }
}
