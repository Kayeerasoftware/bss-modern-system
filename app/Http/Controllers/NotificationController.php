<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role');
        
        $notifications = Notification::where('roles', 'like', "%{$role}%")
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'roles' => 'required|array',
            'type' => 'required|in:info,success,warning,error'
        ]);

        $data['created_by'] = Auth::guard('member')->user()->member_id ?? 'system';

        Notification::create($data);

        return response()->json(['success' => true, 'message' => 'Notification sent']);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}