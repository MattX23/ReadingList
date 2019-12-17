<?php

namespace Tests\Feature;

use App\Http\Controllers\ReadingListController;
use App\ReadingList;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
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
            ->assertSee(ReadingListController::DELETED_SUCCESS_MESSAGE);
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
        $this->assertEquals($response->getData(), ReadingListController::DELETED_SUCCESS_MESSAGE);
    }

    public function testUserCanEditReadingList()
    {
        $user = $this->createUserAndReadingLists(2);

        $readingList = $user->readingLists->first();

        $request = Request::create(route('lists.edit', $readingList), 'PUT',[
            'name'     => 'New name',
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user);

        $controller = new ReadingListController();

        $response = $controller->edit($readingList, $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getData(), ReadingListController::UPDATED_SUCCESS_MESSAGE);
    }

    public function testUserCanStoreReadingList()
    {
        $user = factory(User::class)->create();

        $request = Request::create(route('lists.create'), 'POST',[
            'name' => 'New name',
        ]);

        $this->actingAs($user);

        $response = (new ReadingListController())->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getData(), ReadingListController::CREATED_SUCCESS_MESSAGE);
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

    public function testCreateRestoredLinksList()
    {
        $user = $this->createUserAndReadingLists(3);

        $this->actingAs($user);

        $readingList = (new ReadingList())->createRestoredLinksList();

        $user->refresh();

        $this->assertEquals($user->id, $readingList->user_id);
        $this->assertEquals(ReadingList::RESTORED_LIST, $readingList->name);
        $this->assertEquals(count($user->readingLists), $readingList->position);
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
