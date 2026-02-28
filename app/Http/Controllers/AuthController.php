<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Services\AuditLogService;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
            'role' => strtolower(trim((string) $request->input('role'))),
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,ceo,td,cashier,shareholder,client'
        ]);

        // Check if the selected role is active
        $roleStatus = \App\Models\Setting::get('role_status_' . $request->role, 1);
        if ($roleStatus != 1) {
            return back()->withErrors(['role' => 'The selected role is currently inactive. Please contact the administrator.'])->withInput();
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();
        
        // Check if user exists and has the selected role
        if ($user && !$user->hasRole($request->role)) {
            AuditLogService::log($user, 'login_failed', 'Login failed due to role mismatch', [
                'requested_role' => $request->role,
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);
            return back()->withErrors(['role' => 'The selected role does not match your registered role. Please select the correct role.'])->withInput();
        }

        $remember = $request->filled('remember');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            $request->session()->put('active_role', $request->role);

            AuditLogService::log(Auth::user(), 'login', 'User logged into the system', [
                'role' => $request->role,
                'remember' => $remember,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);

            return redirect()->route('login')->with(['login_success' => true, 'login_role' => ucfirst($request->role)]);
        }

        AuditLogService::log($user ?: $request->email, 'login_failed', 'Invalid login credentials', [
            'requested_role' => $request->role,
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,ceo,td,cashier,shareholder,client'
        ]);

        // Check if registration is allowed for this role
        $registrationAllowed = \App\Models\Setting::get('allow_registration_' . $request->role, 1);
        if ($registrationAllowed != 1) {
            return back()->withErrors(['role' => 'Registration for this role is currently disabled. Please contact the administrator.'])->withInput();
        }

        // Check if the selected role is active
        $roleStatus = \App\Models\Setting::get('role_status_' . $request->role, 1);
        if ($roleStatus != 1) {
            return back()->withErrors(['role' => 'The selected role is currently inactive. Please contact the administrator.'])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        // Assign role to user
        $user->assignRole($validated['role']);

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
            'contact' => '',
            'password' => $user->password,
            'role' => $user->role,
            'user_id' => $user->id,
        ]);

        // Assign role to member
        $member->assignRole($validated['role']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('register')->with(['register_success' => true, 'register_role' => ucfirst($validated['role'])]);
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            AuditLogService::log($user, 'logout', 'User logged out of the system', [
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
            ]);
        }

        Auth::logout();
        return redirect('/login');
    }

    public function user()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        return response()->json([
            'user' => $user,
            'role' => $user->role ?? 'client'
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $status = Password::sendResetLink($request->only('email'));
        
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
