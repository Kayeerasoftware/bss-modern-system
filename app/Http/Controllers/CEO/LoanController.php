<?php

namespace App\Http\Controllers\CEO;

use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use Illuminate\Http\Request;

class LoanController extends AdminLoanController 
{
    // CEO can only view, not create/update/delete
    public function create()
    {
        abort(403, 'CEO cannot create loans');
    }
    
    public function store(Request $request)
    {
        abort(403, 'CEO cannot create loans');
    }
    
    public function edit($id)
    {
        abort(403, 'CEO cannot edit loans');
    }
    
    public function update(Request $request, $id)
    {
        abort(403, 'CEO cannot update loans');
    }
    
    public function destroy($id)
    {
        abort(403, 'CEO cannot delete loans');
    }
}
