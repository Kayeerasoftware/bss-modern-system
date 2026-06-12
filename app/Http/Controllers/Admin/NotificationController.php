<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationType;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('created_by', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $typeId = NotificationType::query()->where('name', $request->type)->value('id');
            if ($typeId) {
                $query->where('type_id', $typeId);
            }
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->whereHas('receipts', fn ($q) => $q->where('is_read', 1));
            } elseif ($request->status === 'unread') {
                $query->whereHas('receipts', fn ($q) => $q->where('is_read', 0));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        // Implementation
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        return view('admin.notifications.show', compact('notification'));
    }

    public function edit($id)
    {
        $notification = Notification::findOrFail($id);
        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:sms,email,push,info,success,warning,error',
        ]);

        $typeId = NotificationType::query()->where('name', $validated['type'])->value('id');

        $notification->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type_id' => $typeId,
        ]);
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notification updated successfully');
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted successfully');
    }

    public function history(Request $request)
    {
        $query = Notification::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('created_by', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $typeId = NotificationType::query()->where('name', $request->type)->value('id');
            if ($typeId) {
                $query->where('type_id', $typeId);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->latest()->paginate(15)->appends($request->query());
        return view('admin.notifications.history', compact('notifications'));
    }
}
