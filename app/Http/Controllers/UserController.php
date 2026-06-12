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
        $users = User::select('id', 'username', 'email', 'role_id', 'status', 'created_at')
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
            'role' => 'required|in:admin,ceo,td,cashier,shareholder,client'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        $member = Member::query()->where('user_id', $user->id)->first();
        if ($member) {
            $member->full_name = $user->name;
            $member->email = $user->email;
            $member->membership_status = 'active';
            $member->join_date = $member->join_date ?: now()->toDateString();
            $member->created_by = $member->created_by ?: $user->id;
            $member->saveQuietly();
            $member->assignRole($validated['role']);
        }

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
            'role' => 'sometimes|in:admin,ceo,td,cashier,shareholder,client',
            'status' => 'sometimes|in:active,inactive,suspended,locked'
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
        
        \App\Services\AuditLogService::log(auth()->user(), 'delete', "Deleted user: {$userName}", [
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'entity_identifier' => $user->email,
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
                'ceo' => 'Chief Executive Officer - Executive oversight',
                'td' => 'Technical Director - Projects and technical operations',
                'cashier' => 'Cashier - Handle transactions',
                'shareholder' => 'Shareholder - Investment access',
                'client' => 'Client - Member access'
            ]
        ]);
    }
}
