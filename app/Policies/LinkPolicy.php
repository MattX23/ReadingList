<?php

namespace App\Policies;

use App\Link;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LinkPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function archive(User $user, Link $link): bool
    {
        return $user->id === $link->readingList->user_id;
    }

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function viewArchives(User $user, Link $link): bool
    {
        return $user->id === $link->readingList()->withTrashed()->first()->user_id;
    }

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function edit(User $user, Link $link): bool
    {
        return $user->id === $link->readingList->user_id;
    }

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function restore(User $user, Link $link): bool
    {
        return $user->id === $link->readingList()->withTrashed()->first()->user_id;
    }

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function delete(User $user, Link $link): bool
    {
        return $user->id === $link->readingList()->withTrashed()->first()->user_id;
    }
}
