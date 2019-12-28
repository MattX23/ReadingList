<?php

namespace Tests\Feature;

use App\Link;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCannotEditAnotherUsersLink()
    {
        $user = factory(User::class)->create();

        $anotherUser = $this->createUserWithListsAndLinks(1, 3);

        $link = $anotherUser->readingLists()->first()->links->first();

        $this->actingAs($user)
            ->put(route('link.edit', $link), [
                'name'  => 'New Name',
            ])
            ->assertStatus(403);
    }

    public function testUserCanEditOwnLink()
    {
        $user = $this->createUserWithListsAndLinks(1, 3);

        $link = $user->readingLists()->first()->links->first();

        $this->actingAs($user)
            ->put(route('link.edit', $link), [
                'name'  => 'New Name',
            ])
            ->assertStatus(200)
            ->assertSee(Link::EDITED_SUCCESS_MESSAGE);
    }

    public function testUserCannotArchiveAnotherUsersLink()
    {
        $user = factory(User::class)->create();

        $anotherUser = $this->createUserWithListsAndLinks(1, 3);

        $link = $anotherUser->readingLists()->first()->links->first();

        $this->actingAs($user)
            ->post(route('link.archive', $link))
            ->assertStatus(403)
            ->assertDontSee(Link::ARCHIVED_SUCCESS_MESSAGE);
    }

    public function testUserCanArchiveOwnLink()
    {
        $user = $this->createUserWithListsAndLinks(1, 3);

        $link = $user->readingLists()->first()->links->first();

        $this->actingAs($user)
            ->post(route('link.archive', $link))
            ->assertStatus(200)
            ->assertSee(Link::ARCHIVED_SUCCESS_MESSAGE);
    }

    public function testUserCanDeleteOwnLink()
    {
        $user = $this->createUserWithListsAndLinks(1, 3);

        $link = $user->readingLists()->first()->links->first();

        $this->actingAs($user)
            ->post(route('link.delete', $link->id))
            ->assertStatus(200)
            ->assertSee(Link::DELETED_SUCCESS_MESSAGE);
    }

    public function testUserCannotDeleteAnotherUsersLink()
    {
        $user = factory(User::class)->create();

        $anotherUser = $this->createUserWithListsAndLinks(1, 3);

        $link = $anotherUser->readingLists()->first()->links->first();

        $this->actingAs($user)
            ->post(route('link.delete', $link->id))
            ->assertStatus(403)
            ->assertDontSee(Link::DELETED_SUCCESS_MESSAGE);
    }

    public function testUserCanViewArchives()
    {
        $user = $this->createUserWithListsAndLinks(1, 5);

        $this->actingAs($user);

        foreach ($user->readingLists()->first()->links as $link) {
            $this->post(route('link.delete', $link->id));
        }

        $this->get(route('link.archives', $user->id))
            ->assertStatus(200)
            ->assertSee($user->readingLists()->first()->links);
    }

    public function testLinkIsRestored()
    {
        $user = $this->createUserWithListsAndLinks(1, 5);

        $this->actingAs($user);

        $link = $user->readingLists()->first()->links->first();

        $this->post(route('link.archive', $link));

        $this->put(route('link.restore', $link->id))
            ->assertStatus(200)
            ->assertSee(Link::RESTORED_SUCCESS_MESSAGE);
    }

    public function testUserCanStoreLink()
    {
        $user = (new ReadingListTest())->createUserAndReadingLists(1);

        $this->actingAs($user)
            ->post(route('link.create', [
                'name'              => 'http://www.example.com/',
                'id'                => $user->readingLists()->first()->id,
                'title'             => 'http://www.example.com/',
            ]))
            ->assertStatus(200)
            ->assertSee(Link::SAVED_SUCCESS_MESSAGE);
    }

    public function testUserCannotStoreMalformedLink()
    {
        $user = (new ReadingListTest())->createUserAndReadingLists(1);

        $this->actingAs($user)
            ->post(route('link.create', [
                'name'              => 'wwwexample.com/',
                'id'                => $user->readingLists()->first()->id,
                'title'             => 'http://www.example.com/',
            ]))
            ->assertStatus(302)
            ->assertDontSee(Link::SAVED_SUCCESS_MESSAGE);
    }

    /**
     * @param int $numLists
     * @param int $numLinks
     *
     * @return User
     */
    public function createUserWithListsAndLinks(int $numLists, int $numLinks): User
    {
        $user = (new ReadingListTest)->createUserAndReadingLists($numLists);

        $user->readingLists->each(function ($readingList) use ($numLinks) {
            factory(Link::class, $numLinks)->create([
                'reading_list_id' => $readingList->id,
            ]);
        });

        $user = $this->incrementPositions($user);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    protected function incrementPositions(User $user): User
    {
        $i = 1;
        $user->readingLists->each(function($readingList) use(&$i) {
            $readingList->update([
                'position' => $i
            ]);
            $i++;
        });

        $user->readingLists()->each(function($readingList) {
            $c = 1;
            foreach ($readingList->links as $link) {
                $link->update([
                    'position' => $c,
                ]);
                $c++;
            }
        });

        return $user;
    }
}
