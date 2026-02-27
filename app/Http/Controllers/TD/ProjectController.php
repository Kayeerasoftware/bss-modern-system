<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('manager', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $projects = $query->latest()->paginate(15)->appends($request->query());
        return view('td.projects.index', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return view('td.projects.show', compact('project'));
    }

    public function updateProgress(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update(['progress' => $request->progress]);
        return back()->with('success', 'Project progress updated');
    }
}
