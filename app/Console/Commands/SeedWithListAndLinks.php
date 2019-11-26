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
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('email', '=', 'matt.wood001@gmail.com')->get();

        if ($user) {
            $user->each(function ($user) {
                factory(ReadingList::class, rand(5,10))->create([
                    'user_id'  => $user->id,
                ])->each(function ($readingList) {
                    factory(Link::class, rand(1,8))->create([
                        'reading_list_id' => $readingList->id,
                    ]);
                });
            });
        }
    }
}
