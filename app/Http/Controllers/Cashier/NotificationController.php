<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationReceipt;
use App\Models\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query()
            ->leftJoin('notification_receipts as nr', function ($join) {
                $join->on('notifications.id', '=', 'nr.notification_id')
                    ->where('nr.member_id', '=', auth()->user()?->member?->id);
            })
            ->select('notifications.*', DB::raw('COALESCE(nr.is_read, 0) as is_read'))
            ->orderBy('notifications.created_at', 'desc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        if ($request->status == 'read') {
            $query->where('nr.is_read', true);
        } elseif ($request->status == 'unread') {
            $query->where(function ($q) {
                $q->whereNull('nr.is_read')->orWhere('nr.is_read', false);
            });
        }

        $notifications = $query->paginate(15);

        return view('cashier.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('cashier.notifications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'send_to' => 'required|in:all,role,member'
        ]);

        $sendTo = $request->send_to;
        $users = [];

        if ($sendTo === 'all') {
            $users = \App\Models\User::with('member')->get();
        } elseif ($sendTo === 'role') {
            $users = \App\Models\User::with('member')->get()->filter(fn ($u) => $u->hasRole($request->role));
        } elseif ($sendTo === 'member') {
            $users = \App\Models\User::with('member')->where('id', $request->member_id)->get();
        }

        $notificationTypeId = NotificationType::query()->where('name', 'general')->value('id')
            ?? NotificationType::query()->value('id');

        foreach ($users as $user) {
            $memberId = $user->member?->id;
            if (!$memberId) {
                continue;
            }

            $notification = Notification::create([
                'notification_number' => 'NOT-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                'type_id' => $notificationTypeId,
                'member_id' => $memberId,
                'title' => $request->title,
                'message' => $request->message,
                'created_by' => auth()->id(),
            ]);

            NotificationReceipt::updateOrInsert(
                ['notification_id' => $notification->id, 'member_id' => $memberId],
                ['is_read' => false]
            );
        }

        return redirect()->route('cashier.notifications.index')->with('success', 'Notification sent to ' . $users->count() . ' user(s)');
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        NotificationReceipt::updateOrInsert(
            ['notification_id' => $notification->id, 'member_id' => auth()->user()?->member?->id],
            ['is_read' => true, 'read_at' => now()]
        );
        return view('cashier.notifications.show', compact('notification'));
    }

    public function markAllRead()
    {
        DB::table('notification_receipts')
            ->where('member_id', auth()->user()?->member?->id)
            ->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return redirect()->route('cashier.notifications.index')->with('success', 'Notification deleted');
    }
}
