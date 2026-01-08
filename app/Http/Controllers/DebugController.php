<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;

class DebugController extends Controller
{
    public function testData()
    {
        $data = [
            'members_count' => Member::count(),
            'members_data' => Member::all()->toArray(),
            'total_savings' => Member::sum('savings'),
            'loans_count' => Loan::count(),
            'loans_data' => Loan::all()->toArray(),
            'transactions_count' => Transaction::count(),
            'projects_count' => Project::count(),
        ];
        
        return response()->json($data);
    }
}