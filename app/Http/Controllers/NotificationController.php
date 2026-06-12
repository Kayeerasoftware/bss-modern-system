<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $member = Auth::user()?->member;
        $role = $request->get('role') ?: Auth::user()?->role;
        $roleId = $role ? DB::table('roles')->whereRaw('LOWER(name) = ?', [mb_strtolower(trim((string) $role))])->value('id') : null;

        $query = Notification::query()
            ->leftJoin('notification_receipts as nr', function ($join) use ($member): void {
                $join->on('notifications.id', '=', 'nr.notification_id');

                if ($member) {
                    $join->where('nr.member_id', '=', $member->id);
                } else {
                    $join->whereRaw('1 = 0');
                }
            })
            ->where(function ($q) use ($member, $roleId): void {
                $q->where(function ($scope) use ($member, $roleId): void {
                    if ($member) {
                        $scope->where('notifications.member_id', $member->id);
                    }

                    if ($roleId) {
                        if ($member) {
                            $scope->orWhere('notifications.role_id', $roleId);
                        } else {
                            $scope->where('notifications.role_id', $roleId);
                        }
                    }

                    $scope->orWhere(function ($all): void {
                        $all->whereNull('notifications.member_id')
                            ->whereNull('notifications.role_id');
                    });
                });
            })
            ->select('notifications.*', DB::raw('COALESCE(nr.is_read, 0) as is_read'))
            ->latest('notifications.created_at');

        return response()->json($query->limit(50)->get());
    }

    public function unreadCount()
    {
        $memberId = Auth::user()?->member?->id;
        if (!$memberId) {
            return response()->json(['count' => 0]);
        }

        $count = DB::table('notifications')
            ->leftJoin('notification_receipts as nr', function ($join) use ($memberId): void {
                $join->on('notifications.id', '=', 'nr.notification_id')
                    ->where('nr.member_id', '=', $memberId);
            })
            ->where(function ($q) use ($memberId): void {
                $roleId = DB::table('roles')->whereRaw('LOWER(name) = ?', [mb_strtolower((string) Auth::user()?->role)])->value('id');

                $q->where('notifications.member_id', $memberId)
                    ->orWhere(function ($all) use ($roleId): void {
                        if ($roleId) {
                            $all->where('notifications.role_id', $roleId);
                        }

                        $all->orWhere(function ($generic): void {
                            $generic->whereNull('notifications.member_id')
                                ->whereNull('notifications.role_id');
                        });
                    });
            })
            ->where(function ($q): void {
                $q->whereNull('nr.is_read')->orWhere('nr.is_read', false);
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'This endpoint is handled by the role-specific notification controllers.'], 405);
    }

    public function markAsRead($id)
    {
        $memberId = Auth::user()?->member?->id;
        if (!$memberId) {
            return response()->json(['success' => false], 403);
        }

        DB::table('notification_receipts')->updateOrInsert(
            ['notification_id' => $id, 'member_id' => $memberId],
            ['is_read' => true, 'read_at' => now()]
        );

        return response()->json(['success' => true]);
    }
}
