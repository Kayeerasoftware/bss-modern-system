<?php

namespace App\Http\Controllers\CEO;

use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use Illuminate\Http\Request;

class TransactionController extends AdminTransactionController 
{
    // CEO can only view transactions, not create/update/delete
    public function create()
    {
        abort(403, 'CEO cannot create transactions');
    }
    
    public function store(Request $request)
    {
        abort(403, 'CEO cannot create transactions');
    }
    
    public function edit($id)
    {
        abort(403, 'CEO cannot edit transactions');
    }
    
    public function update(Request $request, $id)
    {
        abort(403, 'CEO cannot update transactions');
    }
    
    public function destroy($id)
    {
        abort(403, 'CEO cannot delete transactions');
    }
}
