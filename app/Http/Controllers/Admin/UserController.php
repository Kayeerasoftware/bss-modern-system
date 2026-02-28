<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use App\Services\ProfilePictureStorageService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->whereHas('member');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhereHas('member', function ($memberQuery) use ($request) {
                      $memberQuery->where('member_id', 'like', "%{$request->search}%")
                          ->orWhere('full_name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->role) {
            $query->whereHas('member', function ($memberQuery) use ($request) {
                $memberQuery->where('role', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', (bool) $request->status)
                ->whereHas('member', function ($memberQuery) use ($request) {
                    $memberQuery->where('status', ((bool) $request->status) ? 'active' : 'inactive');
                });
        }

        $statsBaseQuery = clone $query;
        $userStats = [
            'totalUsers' => (clone $statsBaseQuery)->count(),
            'activeUsers' => (clone $statsBaseQuery)->where('is_active', true)->count(),
            'admins' => (clone $statsBaseQuery)->whereHas('member', fn ($memberQuery) => $memberQuery->where('role', 'admin'))->count(),
            'newThisMonth' => (clone $statsBaseQuery)->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        $users = $query->latest()->paginate(15);
        
        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'))->render();
        }
        
        return view('admin.users.index', compact('users', 'userStats'));
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
            'roles.*' => 'required|in:admin,client,shareholder,cashier,td,ceo',
            'default_role' => 'required|in:admin,client,shareholder,cashier,td,ceo',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $selectedRoles = array_values((array) $request->input('roles', []));
        $defaultRole = strtolower((string) $request->input('default_role', 'client'));
        if (!in_array($defaultRole, $selectedRoles, true)) {
            return back()->withErrors([
                'default_role' => 'Default role must be one of the selected roles.'
            ])->withInput();
        }

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = ProfilePictureStorageService::storeProfilePicture(
                $request->file('profile_picture')
            );
        }

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = $defaultRole;
        $createData = $validated;
        unset($createData['roles'], $createData['default_role']);
        $user = User::create($createData);

        // Assign multiple roles
        if (!empty($selectedRoles)) {
            $user->syncRoles($selectedRoles);
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
        if (!empty($selectedRoles)) {
            $member->syncRoles($selectedRoles);
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
            'roles.*' => 'required|in:admin,client,shareholder,cashier,td,ceo',
            'default_role' => 'required|in:admin,client,shareholder,cashier,td,ceo',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $selectedRoles = array_values((array) $request->input('roles', []));
        $defaultRole = strtolower((string) $request->input('default_role', 'client'));
        if (!in_array($defaultRole, $selectedRoles, true)) {
            return back()->withErrors([
                'default_role' => 'Default role must be one of the selected roles.'
            ])->withInput();
        }

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = ProfilePictureStorageService::storeProfilePicture(
                $request->file('profile_picture'),
                $user->profile_picture
            );
        }

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $updateData = $validated;
        unset($updateData['roles'], $updateData['default_role']);
        $updateData['role'] = $defaultRole;
        $user->update($updateData);

        // Sync roles
        if (!empty($selectedRoles)) {
            $user->syncRoles($selectedRoles);
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
            if (!empty($selectedRoles)) {
                $user->member->syncRoles($selectedRoles);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
