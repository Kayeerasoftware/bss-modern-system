<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Setting;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;

class MembersController extends Controller
{
    public function index()
    {
        if (Setting::get('shareholder_members_access', 1) == 0) {
            return redirect()->route('shareholder.dashboard')
                ->with('error', 'Access to members section has been restricted by administrator.');
        }

        $currentUser = auth()->user();
        $currentMember = $currentUser->member;
        $currentMemberId = $currentMember ? $currentMember->member_id : null;
        
        $query = Member::with('user');

        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('phone', 'like', "%{$search}%");
                  })
                  ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        if (request('role')) {
            $query->where('role', request('role'));
        }

        if (Setting::get('shareholder_hide_savings', 0) == 0) {
            if (request('savings_min')) {
                $query->where('savings', '>=', request('savings_min'));
            }
            if (request('savings_max')) {
                $query->where('savings', '<=', request('savings_max'));
            }
        }

        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        switch (request('sort')) {
            case 'name_asc':
                $query->orderBy('full_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('full_name', 'desc');
                break;
            case 'savings_high':
                if (Setting::get('shareholder_hide_savings', 0) == 0) {
                    $query->orderBy('savings', 'desc');
                } else {
                    $query->latest();
                }
                break;
            case 'savings_low':
                if (Setting::get('shareholder_hide_savings', 0) == 0) {
                    $query->orderBy('savings', 'asc');
                } else {
                    $query->latest();
                }
                break;
            case 'newest':
                $query->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
        }

        $perPage = request('per_page', 20);
        $members = $query->paginate($perPage)->appends(request()->query());

        if ($currentMemberId) {
            foreach ($members as $member) {
                if ($member->member_id !== $currentMemberId) {
                    $member->unread_count = ChatMessage::where('sender_id', $member->member_id)
                        ->where('receiver_id', $currentMemberId)
                        ->where('is_read', false)
                        ->count();
                    
                    $member->last_message = ChatMessage::where(function($q) use ($currentMemberId, $member) {
                        $q->where(function($q2) use ($currentMemberId, $member) {
                            $q2->where('sender_id', $currentMemberId)->where('receiver_id', $member->member_id);
                        })->orWhere(function($q2) use ($currentMemberId, $member) {
                            $q2->where('sender_id', $member->member_id)->where('receiver_id', $currentMemberId);
                        });
                    })->latest()->first();
                } else {
                    $member->unread_count = 0;
                    $member->last_message = null;
                }
            }
        } else {
            foreach ($members as $member) {
                $member->unread_count = 0;
                $member->last_message = null;
            }
        }

        $statsBaseQuery = clone $query;
        $stats = [
            'total' => (clone $statsBaseQuery)->count(),
            'active' => (clone $statsBaseQuery)->where('status', 'active')->count(),
            'shareholders' => (clone $statsBaseQuery)->where(function ($q) {
                $q->where('role', 'shareholder')
                    ->orWhereHas('user', fn($u) => $u->where('role', 'shareholder'));
            })->count(),
            'newThisMonth' => (clone $statsBaseQuery)->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('shareholder.members', compact('members', 'stats'));
    }

    public function show($id)
    {
        if (Setting::get('shareholder_members_access', 1) == 0) {
            return redirect()->route('shareholder.dashboard')
                ->with('error', 'Access to members section has been restricted by administrator.');
        }

        $member = Member::with(['loans', 'transactions', 'shares'])->findOrFail($id);
        return view('shareholder.members.show', compact('member'));
    }
}
