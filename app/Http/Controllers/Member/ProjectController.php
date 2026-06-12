<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function myProjects()
    {
        $projects = [];
        return view('member.projects.my-projects', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return view('member.projects.show', compact('project'));
    }
}
