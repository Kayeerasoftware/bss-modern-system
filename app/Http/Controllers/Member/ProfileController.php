<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BioData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Password updated successfully');
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
