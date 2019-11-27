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

        $anotherUser = factory(User::class)->create();

        $anotherUser->each(function ($anotherUser) {
            factory(ReadingList::class, 2)->create([
                'user_id' => $anotherUser->id,
            ]);
        });

        $readingList = $anotherUser->readingLists->first();

        $this->actingAs($user)
            ->put(route('lists.edit', $readingList), [
                'name'  => 'New Name',
            ])
            ->assertStatus(403);
    }

    public function testUserCanEditOwnReadingList()
    {
        $user = factory(User::class)->create();

        $user->each(function ($user) {
            factory(ReadingList::class, 2)->create([
                'user_id' => $user->id,
            ]);
        });

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

        $anotherUser = factory(User::class)->create();

        $anotherUser->each(function ($anotherUser) {
            factory(ReadingList::class, 2)->create([
                'user_id' => $anotherUser->id,
            ]);
        });

        $readingList = $anotherUser->readingLists->first();

        $this->actingAs($user)
            ->delete(route('lists.delete', $readingList))
            ->assertStatus(403);
    }

    public function testUserCanDeleteOwnReadingList()
    {
        $user = factory(User::class)->create();

        $user->each(function ($user) {
            factory(ReadingList::class, 2)->create([
                'user_id' => $user->id,
            ]);
        });

        $readingList = $user->readingLists->first();

        $this->actingAs($user)
            ->delete(route('lists.delete', $readingList))
            ->assertStatus(200);
    }

    public function testDeleteReadingList()
    {
        $user = factory(User::class)->create();

        $user->each(function ($user) {
            factory(ReadingList::class, 2)->create([
                'user_id' => $user->id,
            ]);
        });

        $numLists = count(ReadingList::where('user_id', $user->id)->get());

        $readingList = $user->readingLists->first();

        $this->actingAs($user);

        $controller = new ReadingListController();

        $response = $controller->delete($readingList->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(count(ReadingList::where('user_id', $user->id)->get()) < $numLists);
        $this->assertEquals($response->getData(), ReadingListController::DELETED_SUCCESS_MESSAGE);
    }

    public function testEditReadingList()
    {
        $user = factory(User::class)->create();

        $user->each(function ($user) {
            factory(ReadingList::class, 2)->create([
                'user_id' => $user->id,
            ]);
        });

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

    public function testStoreReadingList()
    {
        $user = factory(User::class)->create();

        $request = Request::create(route('lists.create'), 'POST',[
            'name' => 'New name',
        ]);

        $this->actingAs($user);

        $controller = new ReadingListController();

        $response = $controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getData(), ReadingListController::CREATED_SUCCESS_MESSAGE);
    }
}
