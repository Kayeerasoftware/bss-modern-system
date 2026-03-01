<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with([
                'member' => static function ($memberQuery) {
                    $memberQuery->withTrashed();
                },
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->withTrashed()
                            ->where('member_id', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('contact', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('role')) {
            $query->where('role', strtolower((string) $request->input('role')));
        }

        if ($request->filled('status')) {
            $status = strtolower((string) $request->input('status'));

            if ($status === 'deleted') {
                $query->whereHas('member', static function ($memberQuery) {
                    $memberQuery->onlyTrashed();
                });
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'unlinked') {
                $query->whereDoesntHave('member');
            }
        }

        $members = $query->latest()->paginate(20)->appends($request->query());

        return view('td.members.index', compact('members'));
    }

    public function show($id)
    {
        $member = User::query()
            ->with([
                'member' => static function ($memberQuery) {
                    $memberQuery->withTrashed();
                },
            ])
            ->findOrFail($id);

        if ($member->member) {
            $member->member->load([
                'loans' => static function ($query) {
                    $query->latest()->take(10);
                },
                'transactions' => static function ($query) {
                    $query->latest()->take(10);
                },
            ]);
        }

        return view('td.members.show', compact('member'));
    }
}
