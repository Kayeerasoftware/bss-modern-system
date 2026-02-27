<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BioData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('member.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        $user = Auth::user();
        $user->update($request->only(['name', 'email', 'phone']));

        if ($user->member && $request->filled('phone')) {
            $user->member->update(['contact' => $request->phone]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
        }

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $newPassword = $request->input('new_password', $request->input('password'));
        $newPasswordConfirmation = $request->input('new_password_confirmation', $request->input('password_confirmation'));

        if ($newPassword && !$request->has('password')) {
            $request->merge([
                'password' => $newPassword,
                'password_confirmation' => $newPasswordConfirmation,
            ]);
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 422);
            }

            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Password updated successfully']);
        }

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function uploadProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = Auth::user();

            if (!$request->hasFile('profile_picture')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
            }

            $file = $request->file('profile_picture');
            if (!$file->isValid()) {
                return response()->json(['success' => false, 'message' => 'Invalid file'], 400);
            }

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $file->store('profile_pictures', 'public');
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

    public function createBioData()
    {
        $member = auth()->user()->member;
        return view('member.bio-data', compact('member'));
    }

    public function storeBioData(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'nin_no' => 'required|string|max:14',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'dob' => 'required|date',
            'nationality' => 'nullable|string',
            'marital_status' => 'required|string',
            'spouse_name' => 'nullable|string',
            'spouse_nin' => 'nullable|string|max:14',
            'next_of_kin' => 'nullable|string',
            'next_of_kin_nin' => 'nullable|string|max:14',
            'father_name' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'children' => 'nullable|array',
            'occupation' => 'nullable|string',
            'signature' => 'required|string',
            'declaration_date' => 'required|date',
        ]);

        $validated['member_id'] = auth()->user()->member->id;
        $validated['present_address'] = [
            'region' => $request->present_region,
            'district' => $request->present_district,
            'county' => $request->present_county,
            'subcounty' => $request->present_subcounty,
            'ward' => $request->present_ward,
            'village' => $request->present_village,
        ];
        $validated['permanent_address'] = [
            'region' => $request->permanent_region,
            'district' => $request->permanent_district,
            'county' => $request->permanent_county,
            'subcounty' => $request->permanent_subcounty,
            'ward' => $request->permanent_ward,
            'village' => $request->permanent_village,
        ];
        $validated['birth_place'] = [
            'region' => $request->birth_region,
            'district' => $request->birth_district,
            'county' => $request->birth_county,
            'subcounty' => $request->birth_subcounty,
            'ward' => $request->birth_ward,
            'village' => $request->birth_village,
        ];

        BioData::updateOrCreate(
            ['member_id' => $validated['member_id']],
            $validated
        );

        return redirect()->route('member.bio-data.view')->with('success', 'Bio data saved successfully');
    }

    public function viewBioData()
    {
        $member = auth()->user()->member;
        return view('member.bio-data-view', compact('member'));
    }
}
