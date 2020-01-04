<?php

namespace Tests\Feature;

use App\Archive;
use App\Link;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\SetUpTrait;

class LinkTest extends TestCase
{
    use RefreshDatabase, SetUpTrait, SoftDeletes;

    public function testUserCannotEditAnotherUsersLink()
    {
        $anotherUser = factory(User::class)->create();

        $this->addListsAndLinks(1, 3);

        $link = $this->user->readingLists()->first()->links->first();

        $this->actingAs($anotherUser)
            ->put(route('link.edit', $link), [
                'name'  => 'New Name',
            ])
            ->assertStatus(403);
    }

    public function testUserCanEditOwnLink()
    {
        $this->addListsAndLinks(1, 3);

        $link = $this->user->readingLists()->first()->links->first();

        $this->actingAs($this->user)
            ->put(route('link.edit', $link), [
                'name'  => 'New Name',
            ])
            ->assertStatus(200)
            ->assertSee(Link::EDITED_SUCCESS_MESSAGE);
    }

    public function testUserCannotArchiveAnotherUsersLink()
    {
        $anotherUser = factory(User::class)->create();

        $this->addListsAndLinks(1, 3);

        $link = $this->user->readingLists()->first()->links->first();

        $this->actingAs($anotherUser)
            ->post(route('link.archive', $link))
            ->assertStatus(403)
            ->assertDontSee(Link::ARCHIVED_SUCCESS_MESSAGE);
    }

    public function testUserCanArchiveOwnLink()
    {
        $this->addListsAndLinks(1, 3);

        $link = $this->user->readingLists()->first()->links->first();

        $this->actingAs($this->user)
            ->post(route('link.archive', $link))
            ->assertStatus(200)
            ->assertSee(Link::ARCHIVED_SUCCESS_MESSAGE);
    }

    public function testUserCanDeleteOwnLink()
    {
        $this->addListsAndLinks(1, 3);

        $link = $this->user->readingLists()->first()->links->first();

        $this->actingAs($this->user)
            ->delete(route('link.delete', $link->id))
            ->assertStatus(200)
            ->assertSee(Link::DELETED_SUCCESS_MESSAGE);
    }

    public function testUserCannotDeleteAnotherUsersLink()
    {
        $anotherUser = factory(User::class)->create();

        $this->addListsAndLinks(1, 3);

        $link = $this->user->readingLists()->first()->links->first();

        $this->actingAs($anotherUser)
            ->delete(route('link.delete', $link->id))
            ->assertStatus(403)
            ->assertDontSee(Link::DELETED_SUCCESS_MESSAGE);
    }

    public function testUserCanViewArchives()
    {
        $this->addListsAndLinks(1, 5);

        $this->actingAs($this->user);

        foreach ($this->user->readingLists()->first()->links as $link) {
            $this->delete(route('link.delete', $link->id));
        }

        $this->get(route('link.archives'))
            ->assertStatus(200)
            ->assertSee($this->user->readingLists()->first()->links);
    }

    public function testLinkIsRestored()
    {
        $this->addListsAndLinks(1, 5);

        $this->actingAs($this->user);

        $link = $this->user->readingLists()->first()->links->first();

        $this->post(route('link.archive', $link->id))
            ->assertStatus(200)
            ->assertSee(Link::ARCHIVED_SUCCESS_MESSAGE);

        $archive = Archive::where('link_id', '=', $link->id)->first();

        $this->put(route('link.restore', $archive->id))
            ->assertStatus(200)
            ->assertSee(Link::RESTORED_SUCCESS_MESSAGE);
    }

    public function testUserCanStoreLink()
    {
        $this->addReadingLists(1);

        $this->actingAs($this->user)
            ->post(route('link.create', [
                'name'              => 'http://www.example.com/',
                'id'                => $this->user->readingLists()->first()->id,
                'title'             => 'http://www.example.com/',
            ]))
            ->assertStatus(200)
            ->assertSee(Link::SAVED_SUCCESS_MESSAGE);
    }

    public function testUserCannotStoreMalformedLink()
    {
        $this->addReadingLists(1);

        $this->actingAs($this->user)
            ->post(route('link.create', [
                'name'              => 'wwwexample.com/',
                'id'                => $this->user->readingLists()->first()->id,
                'title'             => 'http://www.example.com/',
            ]))
            ->assertStatus(302)
            ->assertDontSee(Link::SAVED_SUCCESS_MESSAGE);
    }
}
