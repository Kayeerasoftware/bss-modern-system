<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationsController extends Controller
{
    public function index()
    {
        $query = Notification::query();
        
        // Only show notifications for shareholders or all users
        $query->where(function($q) {
            $q->whereJsonContains('roles', 'shareholder')
              ->orWhereJsonContains('roles', 'all');
        });
        
        // Search functionality
        if (request('search')) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('message', 'like', '%' . request('search') . '%');
            });
        }
        
        // Filter by type
        if (request('type')) {
            $query->where('type', request('type'));
        }
        
        // Filter by status
        if (request('status') == 'read') {
            $query->where('is_read', true);
        } elseif (request('status') == 'unread') {
            $query->where('is_read', false);
        }
        
        $notifications = $query->latest()->paginate(15);
        return view('shareholder.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }
        
        return view('shareholder.notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
}
