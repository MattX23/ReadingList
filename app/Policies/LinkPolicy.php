<?php

namespace App\Policies;

use App\Link;
use App\ReadingList;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

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
     * @param Link $link
     *
     * @return bool
     */
    public function forceDelete(User $user, Link $link): bool
    {
        return $user->id === $link->readingList->user_id;
    }
}
