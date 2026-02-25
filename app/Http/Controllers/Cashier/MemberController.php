<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::latest()->paginate(20);
        return view('cashier.members.index', compact('members'));
    }

    public function show($id)
    {
        $member = Member::with('transactions')->findOrFail($id);
        return view('cashier.members.show', compact('member'));
    }
}
