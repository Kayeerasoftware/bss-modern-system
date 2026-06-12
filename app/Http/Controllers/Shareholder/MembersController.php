<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Setting;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
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
        $currentMemberId = $currentMember ? $currentMember->id : null;
        
        $query = Member::with('user');

        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('primary_phone', 'like', "%{$search}%")
                  ->orWhere('alternative_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('username', 'like', "%{$search}%");
                  })
                  ->orWhere('member_account_number', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%");
            });
        }

        if (request('role')) {
            $role = request('role');
            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $role));
        }

        if (Setting::get('shareholder_hide_savings', 0) == 0) {
            if (request('savings_min')) {
                $query->whereIn('id', function ($sub) {
                    $sub->select('member_id')
                        ->from('savings_accounts')
                        ->where('current_balance', '>=', request('savings_min'));
                });
            }
            if (request('savings_max')) {
                $query->whereIn('id', function ($sub) {
                    $sub->select('member_id')
                        ->from('savings_accounts')
                        ->where('current_balance', '<=', request('savings_max'));
                });
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
                    $query->leftJoin('savings_accounts', 'savings_accounts.member_id', '=', 'members.id')
                        ->select('members.*')
                        ->orderBy('savings_accounts.current_balance', 'desc');
                } else {
                    $query->latest();
                }
                break;
            case 'savings_low':
                if (Setting::get('shareholder_hide_savings', 0) == 0) {
                    $query->leftJoin('savings_accounts', 'savings_accounts.member_id', '=', 'members.id')
                        ->select('members.*')
                        ->orderBy('savings_accounts.current_balance', 'asc');
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
            $memberIds = $members->pluck('id')->filter()->values()->all();
            $conversationIds = ChatParticipant::query()
                ->where('member_id', $currentMemberId)
                ->pluck('conversation_id')
                ->all();

            $memberConversationMap = ChatParticipant::query()
                ->whereIn('conversation_id', $conversationIds)
                ->where('member_id', '!=', $currentMemberId)
                ->whereIn('member_id', $memberIds)
                ->get(['conversation_id', 'member_id'])
                ->pluck('conversation_id', 'member_id');

            $unreadCounts = ChatMessage::query()
                ->selectRaw('conversation_id, COUNT(*) as unread_count')
                ->whereIn('conversation_id', $conversationIds)
                ->where('sender_id', '!=', $currentMemberId)
                ->where('is_read', false)
                ->groupBy('conversation_id')
                ->pluck('unread_count', 'conversation_id');

            $lastMessageRows = ChatMessage::query()
                ->selectRaw('conversation_id, MAX(id) as last_id')
                ->whereIn('conversation_id', $conversationIds)
                ->groupBy('conversation_id')
                ->get();

            $lastMessageMap = ChatMessage::whereIn('id', $lastMessageRows->pluck('last_id')->all())
                ->get()
                ->keyBy('id');

            $lastMessageByConversation = [];
            foreach ($lastMessageRows as $row) {
                $lastMessageByConversation[$row->conversation_id] = $lastMessageMap[$row->last_id] ?? null;
            }

            foreach ($members as $member) {
                if ($member->id !== $currentMemberId) {
                    $conversationId = $memberConversationMap[$member->id] ?? null;
                    $member->unread_count = $conversationId ? (int) ($unreadCounts[$conversationId] ?? 0) : 0;
                    $member->last_message = $conversationId ? ($lastMessageByConversation[$conversationId] ?? null) : null;
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
            'active' => (clone $statsBaseQuery)->where('membership_status', 'active')->count(),
            'shareholders' => (clone $statsBaseQuery)->where(function ($q) {
                $q->whereHas('roles', fn($roleQuery) => $roleQuery->where('name', 'shareholder'));
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
