<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
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
                'location' => $member->location,
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

        // Auto-generate unique member_id
        $lastMember = Member::orderBy('id', 'desc')->first();
        $nextNumber = $lastMember ? intval(substr($lastMember->member_id, 3)) + 1 : 1;
        $validated['member_id'] = 'BSS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        while (Member::where('member_id', $validated['member_id'])->exists()) {
            $nextNumber++;
            $validated['member_id'] = 'BSS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        // Create user account
        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'is_active' => true,
            'profile_picture' => $validated['profile_picture'] ?? null
        ]);

        $validated['user_id'] = $user->id;
        $validated['password'] = Hash::make($validated['password']);

        $member = Member::create($validated);
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

        $member->update($validated);
        return response()->json(['success' => true, 'member' => $member]);
    }

    public function destroy(Member $member)
    {
        $memberName = $member->full_name;
        $memberId = $member->member_id;
        $member->delete();
        
        \DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Member Deleted',
            'details' => "Deleted member: {$memberName} ({$memberId})",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }
}