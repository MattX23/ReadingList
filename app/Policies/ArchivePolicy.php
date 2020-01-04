<?php

namespace App\Policies;

use App\Archive;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArchivePolicy
{
    use HandlesAuthorization;

    /**
     * @param User    $user
     * @param Archive $archive
     *
     * @return bool
     */
    public function delete(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }

    /**
     * @param User    $user
     * @param Archive $archive
     *
     * @return bool
     */
    public function restore(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }
}
