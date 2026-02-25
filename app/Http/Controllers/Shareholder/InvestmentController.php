<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index()
    {
        $investments = InvestmentOpportunity::where('status', 'active')->paginate(10);
        
        $stats = [
            'total' => InvestmentOpportunity::count(),
            'active' => InvestmentOpportunity::where('status', 'active')->count(),
            'total_value' => 0,
            'avg_roi' => 0,
        ];
        
        return view('shareholder.investments.index', compact('investments', 'stats'));
    }

    public function create()
    {
        return view('shareholder.investments.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('shareholder.investments.index')->with('success', 'Investment created successfully');
    }

    public function show($id)
    {
        $investment = InvestmentOpportunity::findOrFail($id);
        return view('shareholder.investments.show', compact('investment'));
    }
}
