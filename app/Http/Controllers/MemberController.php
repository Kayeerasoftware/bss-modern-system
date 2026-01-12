<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        return response()->json(Member::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:members',
            'contact' => 'required|string',
            'location' => 'required|string',
            'occupation' => 'required|string',
            'role' => 'nullable|string'
        ]);

        // Auto-generate unique member_id
        $lastMember = Member::orderBy('id', 'desc')->first();
        $nextNumber = $lastMember ? intval(substr($lastMember->member_id, 3)) + 1 : 1;
        $validated['member_id'] = 'BSS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (Member::where('member_id', $validated['member_id'])->exists()) {
            $nextNumber++;
            $validated['member_id'] = 'BSS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

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