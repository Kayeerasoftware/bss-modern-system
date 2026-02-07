<?php

namespace App\Http\Controllers;

use App\Models\Fundraising;
use Illuminate\Http\Request;

class FundraisingController extends Controller
{
    public function index()
    {
        $fundraisings = Fundraising::orderBy('created_at', 'desc')->get();
        return response()->json($fundraisings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $fundraising = Fundraising::create([
            'campaign_id' => 'FND' . str_pad(Fundraising::count() + 1, 3, '0', STR_PAD_LEFT),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'target_amount' => $validated['target_amount'],
            'raised_amount' => 0,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'active'
        ]);

        return response()->json(['success' => true, 'fundraising' => $fundraising]);
    }

    public function update(Request $request, $id)
    {
        $fundraising = Fundraising::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'target_amount' => 'numeric|min:1000',
            'raised_amount' => 'numeric|min:0',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => 'in:active,completed,cancelled'
        ]);

        $fundraising->update($validated);
        return response()->json(['success' => true, 'fundraising' => $fundraising]);
    }

    public function destroy($id)
    {
        $fundraising = Fundraising::findOrFail($id);
        $campaignTitle = $fundraising->title;
        $fundraising->delete();

        \DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Fundraising Deleted',
            'details' => "Deleted campaign: {$campaignTitle}",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true]);
    }
}
