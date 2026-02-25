<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->paginate(15);
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
