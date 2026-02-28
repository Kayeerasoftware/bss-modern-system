<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:30',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'status' => $validated['status'] ?? 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data' => $project,
        ], 201);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => Project::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->only(['name', 'description', 'budget', 'status', 'progress']));

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function destroy($id)
    {
        Project::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
