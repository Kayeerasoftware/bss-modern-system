<?php

namespace App\Http\Controllers\Admin;

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

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'budget_high':
                $query->orderBy('budget', 'desc');
                break;
            case 'budget_low':
                $query->orderBy('budget', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $projects = $query->paginate(15)->appends($request->query());
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'roi' => 'nullable|numeric',
            'progress' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'manager' => 'nullable|string',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully');
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return view('admin.projects.show', compact('project'));
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'roi' => 'nullable|numeric',
            'progress' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'manager' => 'nullable|string',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully');
    }
}
