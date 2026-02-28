<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\BioData;
use App\Services\ProfilePictureStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('shareholder.profile');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = ProfilePictureStorageService::storeProfilePicture($file, $user->profile_picture);
            $user->update(['profile_picture' => $path]);
        }
        
        // Only update fields that are provided
        $updateData = [];
        if ($request->filled('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->filled('email')) {
            $updateData['email'] = $request->email;
        }
        
        if (!empty($updateData)) {
            $user->update($updateData);
        }
        
        if ($user->member && $request->filled('phone')) {
            $user->member->update(['contact' => $request->phone]);
        }
        
        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
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
    
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect']);
        }
        
        $user->update(['password' => Hash::make($request->new_password)]);
        
        return response()->json(['success' => true, 'message' => 'Password updated successfully']);
    }

    public function createBioData()
    {
        $member = auth()->user()->member;
        return view('shareholder.bio-data', compact('member'));
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

        return redirect()->route('shareholder.bio-data.view')->with('success', 'Bio data saved successfully');
    }

    public function viewBioData()
    {
        $member = auth()->user()->member;
        return view('shareholder.bio-data-view', compact('member'));
    }
}
