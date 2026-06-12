<?php

namespace App\Http\Controllers\CEO;

use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;

class MemberController extends AdminMemberController
{
    // CEO can only view members, not create/update/delete
    public function create()
    {
        abort(403, 'CEO cannot create members');
    }
    
    public function store(StoreMemberRequest $request)
    {
        abort(403, 'CEO cannot create members');
    }
    
    public function edit($id)
    {
        abort(403, 'CEO cannot edit members');
    }
    
    public function update(UpdateMemberRequest $request, $id)
    {
        abort(403, 'CEO cannot update members');
    }
    
    public function destroy($id)
    {
        abort(403, 'CEO cannot delete members');
    }
}
