<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function updated(User $user)
    {
        // Sync profile picture to member when user is updated
        if ($user->isDirty('profile_picture') && $user->member) {
            $user->member->update(['profile_picture' => $user->profile_picture]);
        }
    }
}
