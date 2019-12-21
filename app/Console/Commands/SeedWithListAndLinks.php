<?php

namespace App\Console\Commands;

use App\Link;
use App\ReadingList;
use App\User;
use Illuminate\Console\Command;

class SeedWithListAndLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'readingList:seedOwnList';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a number of lists and links to own profile';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', '=', 'matt.wood001@gmail.com')->first();

        if ($user) {
            $user->each(function (User $user) {
                factory(ReadingList::class, rand(5,10))->create([
                    'user_id'  => $user->id,
                ])->each(function (ReadingList $readingList) {
                    factory(Link::class, rand(1,8))->create([
                        'reading_list_id' => $readingList->id,
                    ]);
                });
            });
        }

        $this->redefinePositions($user);
    }

    /**
     * @param \App\User $user
     */
    protected function redefinePositions(User $user)
    {
        $i = 1;
        $user->readingLists()->each(function(ReadingList $readingList) use (&$i) {
            $readingList->update([
                'position' => $i,
            ]);
            $i++;

            $j = 1;
            $readingList->links()->each(function (Link $link) use (&$j) {
               $link->update([
                   'position' => $j,
               ]);
               $j++;
            });
        });
    }
}
