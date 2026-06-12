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
        $query = User::query()
            ->with(['member', 'roleRecord'])
            ->whereHas('member');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhereHas('member', function ($memberQuery) use ($request) {
                      $memberQuery->where('member_account_number', 'like', "%{$request->search}%")
                          ->orWhere('member_number', 'like', "%{$request->search}%")
                          ->orWhere('full_name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->role) {
            $query->whereHas('roleRecord', fn ($roleQuery) => $roleQuery->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('status', ((bool) $request->status) ? 'active' : 'inactive')
                ->whereHas('member', function ($memberQuery) use ($request) {
                    $memberQuery->where('membership_status', ((bool) $request->status) ? 'active' : 'inactive');
                });
        }

        $statsBaseQuery = clone $query;
        $userStats = [
            'totalUsers' => (clone $statsBaseQuery)->count(),
            'activeUsers' => (clone $statsBaseQuery)->where('status', 'active')->count(),
            'admins' => (clone $statsBaseQuery)->whereHas('roleRecord', fn ($roleQuery) => $roleQuery->where('name', 'admin'))->count(),
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
        $createData['username'] = $createData['name'];
        unset($createData['name'], $createData['phone'], $createData['location']);
        $user = User::withoutEvents(function () use ($createData) {
            return User::create($createData);
        });

        // Assign multiple roles
        if (!empty($selectedRoles)) {
            $user->syncRoles($selectedRoles);
        }

        $member = new Member();
        $member->user_id = $user->id;
        $member->member_number = generate_member_id();
        $member->member_account_number = $member->member_number;
        $member->full_name = $validated['name'];
        $member->email = $user->email;
        $member->primary_phone = $validated['phone'] ?? null;
        $member->place_of_birth = $validated['location'] ?? null;
        $member->occupation = '';
        $member->membership_status = 'active';
        if (!empty($validated['profile_picture'])) {
            $member->profile_picture = $validated['profile_picture'];
        }
        $member->save();

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
        $updateData['username'] = $updateData['name'];
        unset($updateData['name'], $updateData['phone'], $updateData['location']);
        $user->update($updateData);

        // Sync roles
        if (!empty($selectedRoles)) {
            $user->syncRoles($selectedRoles);
        }

        // Sync member
        if ($user->member) {
            $member = $user->member;
            $member->full_name = $validated['name'];
            $member->email = $user->email;
            $member->primary_phone = $validated['phone'] ?? $member->primary_phone;
            $member->place_of_birth = $validated['location'] ?? $member->place_of_birth;
            if (isset($validated['profile_picture'])) {
                $member->profile_picture = $validated['profile_picture'];
            }
            $member->save();
            if (!empty($selectedRoles)) {
                $member->syncRoles($selectedRoles);
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
