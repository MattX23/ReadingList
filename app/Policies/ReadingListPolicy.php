<?php

namespace App\Policies;

use App\ReadingList;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReadingListPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any reading lists.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the reading list.
     *
     * @param  \App\User  $user
     * @param  \App\ReadingList  $readingList
     * @return mixed
     */
    public function view(User $user, ReadingList $readingList)
    {
        //
    }

    /**
     * Determine whether the user can update the reading list.
     *
     * @param  \App\User  $user
     * @param  \App\ReadingList  $readingList
     *
     * @return mixed
     */
    public function edit(User $user, ReadingList $readingList)
    {
        return $user->id === $readingList->user_id;
    }

    /**
     * Determine whether the user can delete the reading list.
     *
     * @param  \App\User  $user
     * @param  \App\ReadingList  $readingList
     * @return mixed
     */
    public function delete(User $user, ReadingList $readingList)
    {
        //
    }

    /**
     * Determine whether the user can restore the reading list.
     *
     * @param  \App\User  $user
     * @param  \App\ReadingList  $readingList
     * @return mixed
     */
    public function restore(User $user, ReadingList $readingList)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the reading list.
     *
     * @param  \App\User  $user
     * @param  \App\ReadingList  $readingList
     * @return mixed
     */
    public function forceDelete(User $user, ReadingList $readingList)
    {
        //
    }
}
