<?php

namespace App\Policies;

use App\ReadingList;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReadingListPolicy
{
    use HandlesAuthorization;

    /**
     * @param User        $user
     * @param ReadingList $readingList
     *
     * @return bool|null
     */
    public function edit(User $user, ReadingList $readingList): ?bool
    {
        return $user->id === $readingList->user_id;
    }

    /**
     * @param User        $user
     * @param ReadingList $readingList
     *
     * @return bool|null
     */
    public function delete(User $user, ReadingList $readingList): ?bool
    {
        return $user->id === $readingList->user_id;
    }
}
