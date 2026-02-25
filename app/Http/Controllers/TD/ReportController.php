<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Member;
use App\Models\Loan;

class ReportController extends Controller
{
    public function index()
    {
        return view('td.reports.index');
    }

    public function projects()
    {
        $projects = Project::all();
        return view('td.reports.projects', compact('projects'));
    }

    public function members()
    {
        $members = Member::all();
        return view('td.reports.members', compact('members'));
    }

    public function loans()
    {
        $loans = Loan::with('member')->get();
        return view('td.reports.loans', compact('loans'));
    }
}
