<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('td.reports.index');
    }

    public function projects(Request $request)
    {
        $query = Project::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $projects = $query->latest()->get();
        return view('td.reports.projects', compact('projects'));
    }

    public function members(Request $request)
    {
        $query = User::query()->with([
            'member' => static function ($memberQuery) {
                $memberQuery->withTrashed();
            },
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                $q->orWhereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery->withTrashed()
                        ->where('member_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $status = strtolower((string) $request->input('status'));
            if ($status === 'deleted') {
                $query->whereHas('member', static function ($memberQuery) {
                    $memberQuery->onlyTrashed();
                });
            } elseif ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'unlinked') {
                $query->whereDoesntHave('member');
            }
        }

        $members = $query->latest()->get();
        return view('td.reports.members', compact('members'));
    }

    public function loans(Request $request)
    {
        $query = Loan::with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_id', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans = $query->latest()->get();
        return view('td.reports.loans', compact('loans'));
    }
}
