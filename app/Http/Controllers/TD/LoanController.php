<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Loan;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with('member')->latest()->paginate(15);
        return view('td.loans.index', compact('loans'));
    }

    public function show($id)
    {
        $loan = Loan::with('member')->findOrFail($id);
        return view('td.loans.show', compact('loan'));
    }
}
