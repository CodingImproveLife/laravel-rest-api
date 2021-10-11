<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param News $news
     * @return bool
     */
    public function update(User $user, News $news)
    {
        return $user->id === $news->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param News $news
     * @return bool
     */
    public function delete(User $user, News $news)
    {
        return $user->id === $news->user_id;
    }
}
