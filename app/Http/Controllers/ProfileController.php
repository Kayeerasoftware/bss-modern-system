<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function uploadProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'member_id' => 'required|string',
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $member = Member::where('member_id', $request->member_id)->firstOrFail();

            if ($request->hasFile('profile_picture')) {
                // Delete old picture if exists
                if ($member->profile_picture && Storage::disk('public')->exists($member->profile_picture)) {
                    Storage::disk('public')->delete($member->profile_picture);
                }

                // Store new picture
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $member->profile_picture = $path;
                $member->save();

                // Sync to user
                if ($member->user) {
                    $member->user->update(['profile_picture' => $path]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'profile_picture_url' => '/storage/' . $path
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getProfilePicture($memberId)
    {
        $member = Member::where('member_id', $memberId)->first();
        
        if ($member && $member->profile_picture) {
            return response()->json([
                'profile_picture_url' => '/storage/' . $member->profile_picture
            ]);
        }

        return response()->json(['profile_picture_url' => null]);
    }
}
