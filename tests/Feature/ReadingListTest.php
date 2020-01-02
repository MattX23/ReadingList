<?php

namespace Tests\Feature;

use App\Http\Controllers\ReadingListController;
use App\Http\Requests\ReadingListRequest;
use App\Link;
use App\ReadingList;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\SetUpTrait;

class ReadingListTest extends TestCase
{
    use RefreshDatabase, SetUpTrait;

    public function testUserCannotEditAnotherUsersReadingList()
    {
        $anotherUser = factory(User::class)->create();

        $this->addListsAndLinks(2);

        $readingList = $anotherUser->readingLists()->first();

        $this->actingAs($this->user)
            ->put(route('lists.edit', $readingList), [
                'name'  => 'New Name',
            ])
            ->assertStatus(403);
    }

    public function testUserCanEditOwnReadingList()
    {
        $this->addListsAndLinks(2);

        $readingList = $this->user->readingLists()->first();

        $this->actingAs($this->user)
            ->put(route('lists.edit', $readingList), [
                'name'  => 'New Name',
            ])
            ->assertStatus(200)
            ->assertSee(ReadingList::UPDATED_SUCCESS_MESSAGE);
    }

    public function testUserCannotDeleteAnotherUsersReadingList()
    {
        $anotherUser = factory(User::class)->create();

        $this->addListsAndLinks(2);

        $readingList = $anotherUser->readingLists()->first();

        $this->actingAs($this->user)
            ->delete(route('lists.delete', $readingList))
            ->assertStatus(403);
    }

    public function testUserCanDeleteOwnReadingList()
    {
        $this->addListsAndLinks(2);

        $readingList = $this->user->readingLists()->first();

        $this->actingAs($this->user)
            ->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);
    }

    public function testListIsOnlySoftDeletedWhenListHasArchivedLink()
    {
        $this->addListsAndLinks(1, 1);

        $this->actingAs($this->user);

        $this->user->readingLists()->first()->links()->first()->delete();

        $readingList = $this->user->readingLists()->first();

        $this->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);

        $this->assertTrue(ReadingList::where('user_id', '=', $this->user->id)->get()->count() < 1);
        $this->assertTrue(ReadingList::withTrashed()->where('user_id', $this->user->id)->get()->count() === 1);
    }

    public function testListIsFullyDeletedWhenListHasNoRelatedLinks()
    {
        $this->addListsAndLinks(1, 1);

        $this->actingAs($this->user);

        $this->user->readingLists()->first()->links()->first()->forceDelete();

        $readingList = $this->user->readingLists()->first();

        $this->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);

        $this->assertTrue(ReadingList::where('user_id', '=', $this->user->id)->get()->count() < 1);
        $this->assertTrue(ReadingList::withTrashed()->where('user_id', $this->user->id)->get()->count() < 1);
    }

    public function testRelatedListIsDeletedWhenLastArchivedLinkIsDeleted()
    {
        $this->addListsAndLinks(1, 1);

        $this->actingAs($this->user);

        $this->user->readingLists()->first()->links()->first()->delete();

        $readingList = $this->user->readingLists()->first();

        $this->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);

        $link = $this->user->readingLists()->withTrashed()->first()->links()->withTrashed()->first();

        $this->post(route('link.deleteFromArchives', $link->id))
            ->assertStatus(200)
            ->assertSee(Link::DELETED_SUCCESS_MESSAGE);

        $this->assertTrue(ReadingList::where('user_id', '=', $this->user->id)->get()->count() < 1);
        $this->assertTrue(ReadingList::withTrashed()->where('user_id', $this->user->id)->get()->count() < 1);
    }

    public function testUserCanStoreReadingList()
    {
        $request = ReadingListRequest::create(route('lists.create'), 'POST',[
            'name' => 'New name',
        ]);

        $this->actingAs($this->user);

        $response = (new ReadingListController())->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getData(), ReadingList::CREATED_SUCCESS_MESSAGE);
    }

    public function testReorderingOfLists()
    {
        $this->addListsAndLinks(5);

        $lists = $this->user->readingLists;

        $i = 1;

        foreach ($lists as $list) {
            $list->update([
                'position' => $i
            ]);
            $i++;
        }

        $positions = $this->user->readingLists->pluck('position')->toArray();

        $this->assertEquals([1,2,3,4,5], $positions);

        $model = new ReadingList();
        $this->actingAs($this->user);

        $ids = $this->user->readingLists->first()->getIds();

        $model->reorderLists(array_reverse($ids));

        $this->user->readingLists->each(function($readingList){
            $readingList->refresh();
        });

        $positions = $this->user->readingLists->pluck('position')->toArray();

        $this->assertEquals([5,4,3,2,1], $positions);
    }

    public function testNewReadingListPositionedCorrectly()
    {
        $this->addListsAndLinks(3);

        $newReadingList = factory(ReadingList::class)->create([
            'user_id'  => $this->user->id,
            'position' => $this->user->getReadingListPosition(),
        ]);

        $this->assertEquals($this->user->readingLists()->count(), $newReadingList->position);
    }
}
