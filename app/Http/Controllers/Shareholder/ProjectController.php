<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
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
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $statusId = DB::table('project_statuses')->where('name', $request->status)->value('id');
            if ($statusId) {
                $query->where('status_id', $statusId);
            }
        }

        if ($request->filled('category')) {
            $category = $request->category;
            if (is_numeric($category)) {
                $query->where('category_id', (int) $category);
            } else {
                $categoryId = DB::table('project_categories')->where('name', $category)->value('id');
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expected_end_date', '<=', $request->date_to);
        }

        $projects = $query->latest()->paginate(15)->appends($request->query());
        return view('shareholder.projects.index', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return view('shareholder.projects.show', compact('project'));
    }
}
