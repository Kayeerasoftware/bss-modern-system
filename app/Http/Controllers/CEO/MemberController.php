<?php

namespace App\Http\Controllers\CEO;

use App\Http\Controllers\Admin\MemberController as AdminMemberController;

class MemberController extends AdminMemberController
{
    // CEO can only view members, not create/update/delete
    public function create()
    {
        abort(403, 'CEO cannot create members');
    }
    
    public function store()
    {
        abort(403, 'CEO cannot create members');
    }
    
    public function edit($id)
    {
        abort(403, 'CEO cannot edit members');
    }
    
    public function update($id)
    {
        abort(403, 'CEO cannot update members');
    }
    
    public function destroy($id)
    {
        abort(403, 'CEO cannot delete members');
    }
}
