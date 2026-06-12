<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    public function index()
    {
        $member = Auth::user()?->member;
        $shareholderRoleId = DB::table('roles')->where('name', 'shareholder')->value('id');

        $query = Notification::query()
            ->leftJoin('notification_receipts as nr', function ($join) use ($member): void {
                $join->on('notifications.id', '=', 'nr.notification_id');

                if ($member) {
                    $join->where('nr.member_id', '=', $member->id);
                } else {
                    $join->whereRaw('1 = 0');
                }
            })
            ->leftJoin('notification_types as nt', 'notifications.type_id', '=', 'nt.id')
            ->where(function ($q) use ($member, $shareholderRoleId): void {
                $q->where(function ($scope) use ($member, $shareholderRoleId): void {
                    if ($member) {
                        $scope->where('notifications.member_id', $member->id);
                    }

                    if ($shareholderRoleId) {
                        if ($member) {
                            $scope->orWhere('notifications.role_id', $shareholderRoleId);
                        } else {
                            $scope->where('notifications.role_id', $shareholderRoleId);
                        }
                    }

                    $scope->orWhere(function ($all): void {
                        $all->whereNull('notifications.member_id')
                            ->whereNull('notifications.role_id');
                    });
                });
            })
            ->select('notifications.*', DB::raw('COALESCE(nr.is_read, 0) as is_read'))
            ->selectRaw('nt.name as type_name')
            ->latest('notifications.created_at');

        if (request('search')) {
            $query->where(function ($q): void {
                $q->where('notifications.title', 'like', '%' . request('search') . '%')
                    ->orWhere('notifications.message', 'like', '%' . request('search') . '%');
            });
        }

        if (request('type')) {
            $typeId = NotificationType::query()->where('name', request('type'))->value('id');
            if ($typeId) {
                $query->where('notifications.type_id', $typeId);
            }
        }

        if (request('status') === 'read') {
            $query->where('nr.is_read', true);
        } elseif (request('status') === 'unread') {
            $query->where(function ($q): void {
                $q->whereNull('nr.is_read')
                    ->orWhere('nr.is_read', false);
            });
        }

        $notifications = $query->paginate(15);

        return view('shareholder.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        $this->markReceipt(Auth::user()?->member?->id, $notification->id);

        return view('shareholder.notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $this->markReceipt(Auth::user()?->member?->id, $notification->id);

        return response()->json(['success' => true]);
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
