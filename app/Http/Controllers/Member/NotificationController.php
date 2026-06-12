<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;
        
        $query = Notification::query()
            ->leftJoin('notification_receipts as nr', function ($join) use ($member) {
                $join->on('notifications.id', '=', 'nr.notification_id')
                    ->where('nr.member_id', '=', $member?->id);
            })
            ->where(function ($q) use ($member) {
                $q->where('notifications.member_id', $member?->id)
                  ->orWhereNull('notifications.member_id');
            })
            ->select('notifications.*', DB::raw('COALESCE(nr.is_read, 0) as is_read'))
            ->latest('notifications.created_at');
        
        if (request('search')) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('message', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request('status') == 'read') {
            $query->where('nr.is_read', true);
        } elseif (request('status') == 'unread') {
            $query->where(function ($q) {
                $q->whereNull('nr.is_read')->orWhere('nr.is_read', false);
            });
        }
        
        $notifications = $query->get();
        
        return view('member.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        $this->markReceipt(Auth::user()?->member?->id, $notification->id);
        
        return view('member.notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $this->markReceipt(Auth::user()?->member?->id, $notification->id);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $member = $user->member;

        DB::table('notification_receipts')
            ->where('member_id', $member?->id)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    private function markReceipt(?int $memberId, int $notificationId): void
    {
        if (!$memberId) {
            return;
        }

        DB::table('notification_receipts')->updateOrInsert(
            ['member_id' => $memberId, 'notification_id' => $notificationId],
            ['is_read' => true, 'read_at' => now()]
        );
    }
}
