<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;
        
        $query = Notification::where('member_id', $member->member_id ?? null)
            ->orWhere('member_id', null)
            ->latest();
        
        if (request('search')) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('message', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request('type')) {
            $query->where('type', request('type'));
        }
        
        if (request('status') == 'read') {
            $query->where('is_read', true);
        } elseif (request('status') == 'unread') {
            $query->where('is_read', false);
        }
        
        $notifications = $query->get();
        
        return view('member.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return view('member.notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $member = $user->member;
        
        Notification::where('member_id', $member->member_id ?? null)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }
}
