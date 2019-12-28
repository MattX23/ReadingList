<?php

namespace Tests\Feature;

use App\Http\Controllers\ReadingListController;
use App\Http\Requests\ReadingListRequest;
use App\Link;
use App\ReadingList;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingListTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCannotEditAnotherUsersReadingList()
    {
        $user = factory(User::class)->create();

        $anotherUser = $this->createUserAndReadingLists(2);

        $readingList = $anotherUser->readingLists->first();

        $this->actingAs($user)
            ->put(route('lists.edit', $readingList), [
                'name'  => 'New Name',
            ])
            ->assertStatus(403);
    }

    public function testUserCanEditOwnReadingList()
    {
        $user = $this->createUserAndReadingLists(2);

        $readingList = $user->readingLists->first();

        $this->actingAs($user)
            ->put(route('lists.edit', $readingList), [
                'name'  => 'New Name',
            ])
            ->assertStatus(200);
    }

    public function testUserCannotDeleteAnotherUsersReadingList()
    {
        $user = factory(User::class)->create();

        $anotherUser = $this->createUserAndReadingLists(2);

        $readingList = $anotherUser->readingLists->first();

        $this->actingAs($user)
            ->delete(route('lists.delete', $readingList))
            ->assertStatus(403);
    }

    public function testUserCanDeleteOwnReadingList()
    {
        $user = $this->createUserAndReadingLists(2);

        $readingList = $user->readingLists->first();

        $this->actingAs($user)
            ->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);
    }

    public function testListIsOnlySoftDeletedWhenListHasArchivedLink()
    {
        $user = (new LinkTest())->createUserWithListsAndLinks(1, 1);

        $this->actingAs($user);

        $user->readingLists()->first()->links->first()->delete();

        $readingList = $user->readingLists()->first();

        $this->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);

        $this->assertTrue(ReadingList::where('user_id', '=', $user->id)->get()->count() < 1);
        $this->assertTrue(ReadingList::withTrashed()->where('user_id', $user->id)->get()->count() === 1);
    }

    public function testListIsFullyDeletedWhenListHasNoRelatedLinks()
    {
        $user = (new LinkTest())->createUserWithListsAndLinks(1, 1);

        $this->actingAs($user);

        $user->readingLists()->first()->links->first()->forceDelete();

        $readingList = $user->readingLists()->first();

        $this->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);

        $this->assertTrue(ReadingList::where('user_id', '=', $user->id)->get()->count() < 1);
        $this->assertTrue(ReadingList::withTrashed()->where('user_id', $user->id)->get()->count() < 1);
    }

    public function testRelatedListIsDeletedWhenLastArchivedLinkIsDeleted()
    {
        $user = (new LinkTest())->createUserWithListsAndLinks(1, 1);

        $this->actingAs($user);

        $user->readingLists()->first()->links->first()->delete();

        $readingList = $user->readingLists()->first();

        $this->delete(route('lists.delete', $readingList))
            ->assertStatus(200)
            ->assertSee(ReadingList::DELETED_SUCCESS_MESSAGE);

        $link = $user->readingLists()->withTrashed()->first()->links()->withTrashed()->first();

        $this->post(route('link.forceDelete', $link->id))
            ->assertStatus(200)
            ->assertSee(Link::DELETED_SUCCESS_MESSAGE);

        $this->assertTrue(ReadingList::where('user_id', '=', $user->id)->get()->count() < 1);
        $this->assertTrue(ReadingList::withTrashed()->where('user_id', $user->id)->get()->count() < 1);
    }

    public function testUserCanDeleteReadingList()
    {
        $user = $this->createUserAndReadingLists(2);

        $numLists = count(ReadingList::where('user_id', $user->id)->get());

        $readingList = $user->readingLists->first();

        $this->actingAs($user);

        $controller = new ReadingListController();

        $response = $controller->delete($readingList->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(count(ReadingList::where('user_id', $user->id)->get()) < $numLists);
        $this->assertEquals($response->getData(), ReadingList::DELETED_SUCCESS_MESSAGE);
    }

    public function testUserCanEditReadingList()
    {
        $user = $this->createUserAndReadingLists(2);

        $readingList = $user->readingLists->first();

        $request = ReadingListRequest::create(route('lists.edit', $readingList), 'PUT',[
            'name'     => 'New name',
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user);

        $controller = new ReadingListController();

        $response = $controller->edit($readingList, $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getData(), ReadingList::UPDATED_SUCCESS_MESSAGE);
    }

    public function testUserCanStoreReadingList()
    {
        $user = factory(User::class)->create();

        $request = ReadingListRequest::create(route('lists.create'), 'POST',[
            'name' => 'New name',
        ]);

        $this->actingAs($user);

        $response = (new ReadingListController())->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getData(), ReadingList::CREATED_SUCCESS_MESSAGE);
    }

    public function testReorderingOfLists()
    {
        $user = $this->createUserAndReadingLists(5);

        $lists = $user->readingLists;

        $i = 1;

        foreach ($lists as $list) {
            $list->update([
                'position' => $i
            ]);
            $i++;
        }

        $positions = $user->readingLists->pluck('position')->toArray();

        $this->assertEquals([1,2,3,4,5], $positions);

        $model = new ReadingList();

        $this->actingAs($user);

        $ids = (new ReadingList())->getReadingListIds();

        $model->reorderLists(array_reverse($ids));

        $user->readingLists->each(function($readingList){
            $readingList->refresh();
        });

        $positions = $user->readingLists->pluck('position')->toArray();

        $this->assertEquals([5,4,3,2,1], $positions);
    }

    public function testNewReadingListPositionedCorrectly()
    {
        $user = $this->createUserAndReadingLists(3);

        $model = new ReadingList();

        $newReadingList = factory(ReadingList::class)->create([
            'user_id'  => $user->id,
            'position' => $model->getNewReadingListPosition($user),
        ]);

        $this->assertEquals(count($user->readingLists), $newReadingList->position);
    }

    /**
     * @param int $numLists
     *
     * @return User
     */
    public function createUserAndReadingLists(int $numLists): User
    {
        $user = factory(User::class)->create();

        $user->each(function ($user) use ($numLists) {
            factory(ReadingList::class, $numLists)->create([
                'user_id' => $user->id,
            ]);
        });

        return $user;
    }
}
