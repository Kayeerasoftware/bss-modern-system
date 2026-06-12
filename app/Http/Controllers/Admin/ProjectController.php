<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_text', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('statusRelation', fn ($q) => $q->where('name', $request->status));
        }

        if ($request->filled('category')) {
            if (is_numeric($request->category)) {
                $query->where('category_id', $request->category);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expected_end_date', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'budget_high':
                $query->orderBy('budget_amount', 'desc');
                break;
            case 'budget_low':
                $query->orderBy('budget_amount', 'asc');
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

        $statusId = ProjectStatus::query()->where('name', strtolower((string) $validated['status']))->value('id');
        $categoryId = is_numeric($validated['category']) ? (int) $validated['category'] : null;

        Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $categoryId,
            'status_id' => $statusId,
            'budget_amount' => $validated['budget'],
            'actual_roi' => $validated['roi'] ?? null,
            'progress_percentage' => $validated['progress'] ?? 0,
            'start_date' => $validated['start_date'],
            'expected_end_date' => $validated['end_date'] ?? null,
            'location_text' => $validated['location'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

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

        $statusId = ProjectStatus::query()->where('name', strtolower((string) $validated['status']))->value('id');
        $categoryId = is_numeric($validated['category']) ? (int) $validated['category'] : null;

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $categoryId,
            'status_id' => $statusId,
            'budget_amount' => $validated['budget'],
            'actual_roi' => $validated['roi'] ?? null,
            'progress_percentage' => $validated['progress'] ?? 0,
            'start_date' => $validated['start_date'],
            'expected_end_date' => $validated['end_date'] ?? null,
            'location_text' => $validated['location'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully');
    }
}
