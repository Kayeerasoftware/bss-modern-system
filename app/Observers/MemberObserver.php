<?php

namespace App\Observers;

use App\Models\Member;

class MemberObserver
{
    public function updated(Member $member)
    {
        // Sync profile picture to user when member is updated
        if ($member->isDirty('profile_picture') && $member->user) {
            $member->user->update(['profile_picture' => $member->profile_picture]);
        }
    }
}
