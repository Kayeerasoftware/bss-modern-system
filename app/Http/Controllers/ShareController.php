<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\Member;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function index()
    {
        $shares = Share::with('member')->orderBy('created_at', 'desc')->get();
        return response()->json($shares);
    }

    public function store(Request $request)
    {
        $share = Share::create([
            'member_id' => 'required|exists:members,member_id',
            'shares_owned' => $request->shares_owned,
            'share_value' => $request->share_value ?? 10000,
            'purchase_date' => $request->purchase_date ?? now(),
            'certificate_number' => 'CERT' . time()
        ]);

        return response()->json($share->load('member'));
    }

    public function getByMember($memberId)
    {
        $shares = Share::where('member_id', $memberId)->get();
        return response()->json($shares);
    }

    public function transferShares(Request $request)
    {
        $fromMember = $request->from_member_id;
        $toMember = $request->to_member_id;
        $sharesToTransfer = $request->shares_count;

        $fromShare = Share::where('member_id', $fromMember)->first();
        
        if (!$fromShare || $fromShare->shares_owned < $sharesToTransfer) {
            return response()->json(['error' => 'Insufficient shares'], 400);
        }

        // Reduce shares from sender
        $fromShare->decrement('shares_owned', $sharesToTransfer);

        // Add shares to receiver
        $toShare = Share::where('member_id', $toMember)->first();
        if ($toShare) {
            $toShare->increment('shares_owned', $sharesToTransfer);
        } else {
            Share::create([
                'member_id' => $toMember,
                'shares_owned' => $sharesToTransfer,
                'share_value' => $fromShare->share_value,
                'purchase_date' => now(),
                'certificate_number' => 'CERT' . time()
            ]);
        }

        return response()->json(['message' => 'Shares transferred successfully']);
    }

    public function summary()
    {
        $summary = [
            'total_shares' => Share::sum('shares_owned'),
            'total_value' => Share::selectRaw('SUM(shares_owned * share_value) as total')->first()->total,
            'total_shareholders' => Share::distinct('member_id')->count(),
            'average_holding' => Share::avg('shares_owned')
        ];

        return response()->json($summary);
    }
}