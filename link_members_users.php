<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\User;

// Link existing members to users by creating user accounts for them
$members = Member::whereNull('user_id')->get();

foreach ($members as $member) {
    // Check if user with this email already exists
    $user = User::where('email', $member->email)->first();
    
    if (!$user) {
        // Create new user for this member
        $user = User::create([
            'name' => $member->full_name,
            'email' => $member->email,
            'password' => Hash::make('password123'), // Default password
            'role' => $member->role ?? 'client',
            'is_active' => true,
            'profile_picture' => $member->profile_picture
        ]);
    }
    
    // Link member to user
    $member->user_id = $user->id;
    $member->save();
    
    echo "Linked {$member->full_name} to user account\n";
}

echo "Done! Linked " . $members->count() . " members to user accounts.\n";
