<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $users = $query->latest()->paginate(15);
        
        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'))->render();
        }
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|in:client,shareholder,cashier,td,ceo',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = $request->roles[0] ?? 'client';
        $user = User::create($validated);

        // Assign multiple roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        // Auto-create member with BSS-C15-000x format
        $lastMember = Member::withTrashed()
            ->where('member_id', 'like', 'BSS-C15-%')
            ->orderBy('member_id', 'desc')
            ->first();
        
        if ($lastMember && preg_match('/BSS-C15-(\d+)/', $lastMember->member_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        $member = Member::create([
            'member_id' => 'BSS-C15-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
            'full_name' => $user->name,
            'email' => $user->email,
            'contact' => $user->phone ?? '',
            'location' => $user->location ?? '',
            'occupation' => '',
            'password' => $user->password,
            'role' => $user->role,
            'profile_picture' => $user->profile_picture,
            'user_id' => $user->id,
        ]);

        // Sync member roles
        if ($request->has('roles')) {
            $member->syncRoles($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|in:client,shareholder,cashier,td,ceo',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $updateData = $validated;
        unset($updateData['roles']);
        $updateData['role'] = $request->roles[0] ?? 'client';
        $user->update($updateData);

        // Sync roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        // Sync member
        if ($user->member) {
            $memberData = [
                'full_name' => $user->name,
                'email' => $user->email,
                'contact' => $user->phone ?? '',
                'location' => $user->location ?? '',
                'role' => $user->role,
            ];
            if (isset($validated['profile_picture'])) {
                $memberData['profile_picture'] = $validated['profile_picture'];
            }
            if ($request->filled('password')) {
                $memberData['password'] = $validated['password'];
            }
            $user->member->update($memberData);
            if ($request->has('roles')) {
                $user->member->syncRoles($request->roles);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete associated member
        if ($user->member) {
            $user->member->delete();
        }
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
