<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    public function index()
    {
        $activeStatusId = DB::table('investment_statuses')->where('name', 'active')->value('id');
        $investments = InvestmentOpportunity::query()
            ->when($activeStatusId, fn ($q) => $q->where('status_id', $activeStatusId))
            ->paginate(10);
        
        $stats = [
            'total' => InvestmentOpportunity::count(),
            'active' => $activeStatusId ? InvestmentOpportunity::where('status_id', $activeStatusId)->count() : 0,
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
