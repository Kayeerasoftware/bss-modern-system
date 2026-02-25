<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->paginate(15);
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
            'status' => 'required|in:pending,sent,failed',
        ]);
        
        $notification->update($validated);
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notification updated successfully');
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted successfully');
    }

    public function history()
    {
        $notifications = Notification::latest()->paginate(15);
        return view('admin.notifications.history', compact('notifications'));
    }
}
