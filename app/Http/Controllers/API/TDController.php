<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;

class TDController extends Controller
{
    public function dashboard()
    {
        $total = Project::count();
        $activeStatusId = ProjectStatus::query()->where('name', 'active')->value('id');
        $completedStatusId = ProjectStatus::query()->where('name', 'completed')->value('id');
        $active = $activeStatusId ? Project::where('status_id', $activeStatusId)->count() : 0;
        $completed = $completedStatusId ? Project::where('status_id', $completedStatusId)->count() : 0;

        return response()->json([
            'success' => true,
            'stats' => [
                'totalProjects' => $total,
                'activeProjects' => $active,
                'completedProjects' => $completed,
                'pendingProjects' => max($total - $active - $completed, 0),
            ],
        ]);
    }

    public function getProjects()
    {
        return response()->json([
            'success' => true,
            'projects' => Project::latest()->paginate(20),
        ]);
    }

    public function updateProgress(Request $request, $id)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $project = Project::findOrFail($id);
        $project->progress = $validated['progress'];
        if ($validated['progress'] >= 100) {
            $project->status = 'completed';
        }
        $project->save();

        return response()->json([
            'success' => true,
            'message' => 'Project progress updated.',
            'project' => $project,
        ]);
    }
}
