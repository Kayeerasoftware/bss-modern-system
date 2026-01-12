<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'target' => 'required|string',
            'method' => 'nullable|string',
            'priority' => 'nullable|string',
        ]);

        $roles = $validated['target'] === 'all' ? ['client', 'shareholder', 'cashier', 'td', 'ceo'] : [$validated['target']];
        
        $notification = DB::table('notifications')->insertGetId([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'roles' => json_encode($roles),
            'type' => $this->mapPriorityToType($validated['priority'] ?? 'normal'),
            'is_read' => false,
            'created_by' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'id' => $notification]);
    }

    public function history()
    {
        $notifications = DB::table('notifications')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                $rolesRaw = $n->roles;
                $roles = [];
                
                $decoded = json_decode($rolesRaw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $roles = $decoded;
                } else {
                    $doubleDecoded = json_decode(json_decode($rolesRaw, true), true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($doubleDecoded)) {
                        $roles = $doubleDecoded;
                    }
                }
                
                $recipientCount = count($roles);
                
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'recipients' => $recipientCount >= 5 ? 'All Members' : ucfirst(implode(', ', $roles)),
                    'method' => 'system',
                    'priority' => $this->mapTypeToPriority($n->type),
                    'status' => 'delivered',
                    'sent_at' => date('M d, Y g:i A', strtotime($n->created_at)),
                ];
            });

        return response()->json($notifications);
    }

    public function stats()
    {
        $total = DB::table('notifications')->count();
        $unread = DB::table('notifications')->where('is_read', false)->count();
        
        return response()->json([
            'total' => $total,
            'unread' => $unread,
            'delivered' => $total,
            'pending' => 0,
            'failed' => 0,
        ]);
    }

    public function resend($id)
    {
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = DB::table('notifications')->find($id);
        if (!$notification) {
            return response()->json(['success' => false], 404);
        }
        
        $title = $notification->title;
        DB::table('notifications')->where('id', $id)->delete();
        
        DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Notification Deleted',
            'details' => "Deleted notification: {$title}",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    private function mapPriorityToType($priority)
    {
        return match($priority) {
            'urgent' => 'error',
            'high' => 'warning',
            'normal' => 'info',
            'low' => 'info',
            default => 'info',
        };
    }

    private function mapTypeToPriority($type)
    {
        return match($type) {
            'error' => 'urgent',
            'warning' => 'high',
            'success' => 'normal',
            'info' => 'normal',
            default => 'normal',
        };
    }
}
