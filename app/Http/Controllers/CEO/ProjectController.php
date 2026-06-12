<?php

namespace App\Http\Controllers\CEO;

use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use Illuminate\Http\Request;

class ProjectController extends AdminProjectController 
{
    // CEO can only view projects, not create/update/delete
    public function create()
    {
        abort(403, 'CEO cannot create projects');
    }
    
    public function store(Request $request)
    {
        abort(403, 'CEO cannot create projects');
    }
    
    public function edit($id)
    {
        abort(403, 'CEO cannot edit projects');
    }
    
    public function update(Request $request, $id)
    {
        abort(403, 'CEO cannot update projects');
    }
    
    public function destroy($id)
    {
        abort(403, 'CEO cannot delete projects');
    }
}
