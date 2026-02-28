<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProfilePictureStorageService;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('td.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $user->update($request->only(['name', 'email', 'phone']));

        if ($user->member && $request->filled('phone')) {
            $user->member->update(['contact' => $request->phone]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
        }

        return back()->with('success', 'Profile updated successfully');
    }

    public function uploadProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = auth()->user();

            if (!$request->hasFile('profile_picture')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
            }

            $file = $request->file('profile_picture');
            if (!$file->isValid()) {
                return response()->json(['success' => false, 'message' => 'Invalid file'], 400);
            }

            $path = ProfilePictureStorageService::storeProfilePicture($file, $user->profile_picture);
            if (!$path) {
                return response()->json(['success' => false, 'message' => 'Failed to store file'], 500);
            }

            $user->update(['profile_picture' => $path]);
            if ($user->member) {
                $user->member->update(['profile_picture' => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'profile_picture_url' => $user->fresh()->profile_picture_url
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
