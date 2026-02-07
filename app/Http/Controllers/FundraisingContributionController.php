<?php

namespace App\Http\Controllers;

use App\Models\FundraisingContribution;
use App\Models\Fundraising;
use Illuminate\Http\Request;

class FundraisingContributionController extends Controller
{
    public function index($fundraisingId = null)
    {
        $query = FundraisingContribution::with('fundraising');
        
        if ($fundraisingId) {
            $query->where('fundraising_id', $fundraisingId);
        }
        
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fundraising_id' => 'required|exists:fundraisings,id',
            'contributor_name' => 'nullable|string',
            'contributor_email' => 'nullable|email',
            'contributor_phone' => 'nullable|string',
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $lastContribution = FundraisingContribution::latest('id')->first();
        $nextNumber = $lastContribution ? intval(substr($lastContribution->contribution_id, 3)) + 1 : 1;
        $validated['contribution_id'] = 'CTB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $contribution = FundraisingContribution::create($validated);

        $fundraising = Fundraising::find($validated['fundraising_id']);
        $fundraising->raised_amount += $validated['amount'];
        $fundraising->save();

        return response()->json(['success' => true, 'contribution' => $contribution]);
    }

    public function destroy($id)
    {
        $contribution = FundraisingContribution::findOrFail($id);
        $fundraising = $contribution->fundraising;
        $fundraising->raised_amount -= $contribution->amount;
        $fundraising->save();
        
        $contribution->delete();
        return response()->json(['success' => true]);
    }
}
