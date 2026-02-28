<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->latest()
            ->get();
            
        return response()->json(['success' => true, 'data' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,manager,treasurer,secretary,member'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true
        ]);

        $lastMember = Member::withTrashed()
            ->where('member_id', 'like', 'BSS-C15-%')
            ->orderBy('member_id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastMember && preg_match('/BSS-C15-(\d+)/', (string) $lastMember->member_id, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        Member::create([
            'member_id' => 'BSS-C15-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT),
            'full_name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:admin,manager,treasurer,secretary,member',
            'is_active' => 'sometimes|boolean'
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();
        
        \DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'User Deleted',
            'details' => "Deleted user: {$userName}",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function getRoles()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'admin' => 'Administrator - Full system access',
                'manager' => 'Manager - Manage members and operations',
                'treasurer' => 'Treasurer - Handle finances and loans',
                'secretary' => 'Secretary - Manage records and meetings',
                'member' => 'Member - Basic access'
            ]
        ]);
    }
}
