<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status == 'read') {
            $query->where('is_read', true);
        } elseif ($request->status == 'unread') {
            $query->where('is_read', false);
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
            'type' => 'required|in:info,success,warning,error',
            'send_to' => 'required|in:all,role,member'
        ]);

        $sendTo = $request->send_to;
        $users = [];

        if ($sendTo === 'all') {
            $users = \App\Models\User::all();
        } elseif ($sendTo === 'role') {
            $users = \App\Models\User::where('role', $request->role)->get();
        } elseif ($sendTo === 'member') {
            $users = \App\Models\User::where('id', $request->member_id)->get();
        }

        foreach ($users as $user) {
            Notification::create([
                'member_id' => $user->id,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'roles' => [$user->role],
                'created_by' => auth()->user()->name
            ]);
        }

        return redirect()->route('cashier.notifications.index')->with('success', 'Notification sent to ' . $users->count() . ' user(s)');
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);
        return view('cashier.notifications.show', compact('notification'));
    }

    public function markAllRead()
    {
        Notification::where('member_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Notification::where('member_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->route('cashier.notifications.index')->with('success', 'Notification deleted');
    }
}
