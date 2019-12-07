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
        return $user->id === $link->readingList->user_id;
    }

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function move(User $user, Link $link): bool
    {
        return $user->id === $link->readingList->user_id;
    }

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function rename(User $user, Link $link): bool
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
        return $user->id === $link->readingList->user_id;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function store(User $user): bool
    {
        return $user->exists;
    }

    /**
     * @param User $user
     * @param Link $link
     *
     * @return bool
     */
    public function forceDelete(User $user, Link $link): bool
    {
        return $user->id === $link->readingList->user_id;
    }
}
