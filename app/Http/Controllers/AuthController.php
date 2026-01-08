<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'user' => $user,
                'role' => $user->role ?? 'member'
            ]);
        }

        // Try member login
        $member = Member::where('email', $credentials['email'])->first();
        if ($member && Hash::check($credentials['password'], $member->password)) {
            return response()->json([
                'success' => true,
                'user' => $member,
                'role' => $member->role ?? 'client'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'member'
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'user' => $user,
            'role' => $user->role
        ]);
    }

    public function logout()
    {
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
}