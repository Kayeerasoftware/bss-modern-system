<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('member.settings.index');
    }

    public function update(Request $request)
    {
        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
