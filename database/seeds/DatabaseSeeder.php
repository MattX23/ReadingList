<?php

use App\Link;
use App\ReadingList;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 10)->create()->each(function ($user) {
            factory(ReadingList::class, 2)
                ->create([
                    'user_id'  => $user->id,
                ])->each(function ($readingList) {
                    factory(Link::class, 5)
                        ->create([
                            'reading_list_id' => $readingList->id,
                        ]);
                });
            });

        $users = User::all();

        foreach ($users as $user) {
            $i = 1;
            foreach ($user->readingLists as $readingList) {
                $readingList->update([
                    'position' => $i
                ]);
                $i++;

                $c = 1;
                foreach ($readingList->links as $link) {
                    $link->update([
                        'position' => $c
                    ]);
                    $c++;
                }
            }
        }
    }
}
