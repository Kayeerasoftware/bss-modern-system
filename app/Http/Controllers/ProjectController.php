<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('created_at', 'desc')->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'planning',
            'progress' => 0
        ]);

        return response()->json($project);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        return response()->json($project);
    }

    public function updateProgress(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update([
            'progress' => $request->progress,
            'status' => $request->progress >= 100 ? 'completed' : 'in_progress'
        ]);
        return response()->json($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $projectName = $project->name;
        $project->delete();
        
        \DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Project Deleted',
            'details' => "Deleted project: {$projectName}",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['message' => 'Project deleted successfully']);
    }
}