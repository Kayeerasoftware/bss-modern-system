<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Services\ProfilePictureStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with('user')->get()->map(function($member) {
            return [
                'id' => $member->id,
                'member_id' => $member->member_id,
                'full_name' => $member->full_name,
                'email' => $member->email,
                'contact' => $member->contact,
                'location' => $member->place_of_birth,
                'occupation' => $member->occupation,
                'role' => $member->role,
                'savings' => $member->savings,
                'loan' => $member->loan,
                'savings_balance' => $member->savings_balance,
                'profile_picture' => $member->profile_picture,
                'created_at' => $member->created_at,
                'user_id' => $member->user_id,
                'user_role' => $member->user ? $member->user->role : null,
                'user_status' => $member->user ? ($member->user->is_active ? 'active' : 'inactive') : null
            ];
        })->values();
        return response()->json($members);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:members|unique:users',
            'contact' => 'nullable|string',
            'location' => 'nullable|string',
            'occupation' => 'nullable|string',
            'role' => 'nullable|string',
            'savings' => 'nullable|numeric',
            'password' => 'required|string|min:6'
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = ProfilePictureStorageService::storeProfilePicture($request->file('profile_picture'));
            $validated['profile_picture'] = $path;
        }

        // Create user account
        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'client',
            'status' => 'active',
        ]);

        $member = Member::query()->where('user_id', $user->id)->first();
        if ($member) {
            $member->full_name = $validated['full_name'];
            $member->email = $validated['email'];
            $member->primary_phone = $validated['contact'] ?? null;
            $member->place_of_birth = $validated['location'] ?? null;
            $member->occupation = $validated['occupation'] ?? null;
            $member->profile_picture = $validated['profile_picture'] ?? null;
            $member->membership_status = 'active';
            $member->join_date = $member->join_date ?: now()->toDateString();
            $member->created_by = $member->created_by ?: $user->id;
            $member->saveQuietly();
            $member->assignRole($validated['role'] ?? 'client');
        }
        return response()->json(['success' => true, 'member' => $member]);
    }

    public function show(Member $member)
    {
        return response()->json($member);
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'contact' => 'required|string',
            'location' => 'required|string',
            'occupation' => 'required|string'
        ]);

        $member->full_name = $validated['full_name'];
        $member->email = $validated['email'];
        $member->primary_phone = $validated['contact'];
        $member->place_of_birth = $validated['location'];
        $member->occupation = $validated['occupation'];
        $member->save();
        return response()->json(['success' => true, 'member' => $member]);
    }

    public function destroy(Member $member)
    {
        $memberName = $member->full_name;
        $memberId = $member->member_id;
        $member->delete();
        
        \App\Services\AuditLogService::log(auth()->user(), 'delete', "Deleted member: {$memberName} ({$memberId})", [
            'entity_type' => 'member',
            'entity_id' => $member->id,
            'entity_identifier' => $member->member_number,
        ]);
        
        return response()->json(['success' => true]);
    }
}
