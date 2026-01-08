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
            'member_id' => 'required|string|unique:members',
            'full_name' => 'required|string',
            'email' => 'required|email|unique:members',
            'contact' => 'required|string',
            'location' => 'required|string',
            'occupation' => 'required|string'
        ]);

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
        $member->delete();
        return response()->json(['success' => true]);
    }
}