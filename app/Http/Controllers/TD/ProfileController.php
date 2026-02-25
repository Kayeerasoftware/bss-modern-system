<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('td.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $user->update($request->only(['name', 'email', 'phone']));
        return back()->with('success', 'Profile updated successfully');
    }
}
