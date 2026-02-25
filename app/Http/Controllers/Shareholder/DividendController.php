<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Dividend;
use App\Models\Member;

class DividendController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();
        $dividends = Dividend::where('member_id', $member?->member_id)->latest()->paginate(10);
        return view('shareholder.dividends.index', compact('dividends'));
    }
}
