<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'target' => 'required|string',
            'member_id' => 'nullable|integer|exists:members,id',
            'method' => 'nullable|string',
            'priority' => 'nullable|string',
        ]);

        $typeName = $this->mapPriorityToType($validated['priority'] ?? 'normal');
        $typeId = NotificationType::query()->where('name', $typeName)->value('id')
            ?? NotificationType::query()->value('id');

        $target = strtolower(trim((string) $validated['target']));
        $memberIds = collect();
        $roleId = null;

        if ($target === 'all') {
            $memberIds = DB::table('members')->orderBy('id')->pluck('id');
        } elseif ($target === 'member' && !empty($validated['member_id'])) {
            $memberIds = collect([(int) $validated['member_id']]);
        } else {
            $roleId = DB::table('roles')
                ->whereRaw('LOWER(name) = ?', [$target])
                ->value('id');

            if ($roleId) {
                $memberIds = DB::table('member_roles')
                    ->where('role_id', $roleId)
                    ->pluck('member_id')
                    ->unique()
                    ->values();
            } elseif (!empty($validated['member_id'])) {
                $memberIds = collect([(int) $validated['member_id']]);
            } else {
                $memberIds = DB::table('members')->orderBy('id')->pluck('id');
            }
        }

        $createdIds = [];
        foreach ($memberIds as $memberId) {
            $notificationId = DB::table('notifications')->insertGetId([
                'notification_number' => 'NOT-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                'type_id' => $typeId,
                'member_id' => $memberId,
                'role_id' => $roleId,
                'title' => $validated['title'],
                'message' => $validated['message'],
                'created_by' => auth()->id() ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('notification_receipts')->updateOrInsert(
                ['notification_id' => $notificationId, 'member_id' => $memberId],
                ['is_read' => false, 'created_at' => now()]
            );

            $createdIds[] = $notificationId;
        }

        return response()->json(['success' => true, 'ids' => $createdIds]);
    }

    public function markAsRead($id)
    {
        $memberId = Auth::user()?->member?->id;
        if (!$memberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 403);
        }

        DB::table('notification_receipts')->updateOrInsert(
            ['notification_id' => $id, 'member_id' => $memberId],
            ['is_read' => true, 'read_at' => now(), 'created_at' => now()]
        );

        return response()->json(['success' => true]);
    }

    public function history()
    {
        $notifications = DB::table('notifications')
            ->leftJoin('members', 'members.id', '=', 'notifications.member_id')
            ->orderBy('notifications.created_at', 'desc')
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'recipients' => $n->member_account_number ?? $n->member_number ?? 'Member',
                    'method' => 'system',
                    'priority' => 'normal',
                    'status' => 'delivered',
                    'sent_at' => date('M d, Y g:i A', strtotime($n->created_at)),
                ];
            });

        return response()->json($notifications);
    }

    public function stats()
    {
        $total = DB::table('notifications')->count();
        $unread = DB::table('notification_receipts')->where('is_read', false)->count();
        
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
        DB::table('notification_receipts')->where('notification_id', $id)->delete();
        
        \App\Services\AuditLogService::log(auth()->user(), 'delete', "Deleted notification: {$title}", [
            'entity_type' => 'notification',
            'entity_id' => $id,
        ]);
        
        return response()->json(['success' => true]);
    }

    private function mapPriorityToType($priority)
    {
        return match($priority) {
            'urgent' => 'alert',
            'high' => 'warning',
            'normal' => 'general',
            'low' => 'general',
            default => 'general',
        };
    }

    private function mapTypeToPriority($type)
    {
        return 'normal';
    }
}
