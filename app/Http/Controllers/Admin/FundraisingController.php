<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fundraising;
use Illuminate\Http\Request;

class FundraisingController extends Controller
{
    public function index()
    {
        $fundraisings = Fundraising::latest()->paginate(15);
        return view('admin.fundraising.index', compact('fundraisings'));
    }

    public function campaigns()
    {
        $campaigns = Fundraising::where('status', 'active')->latest()->paginate(15);
        return view('admin.fundraising.campaigns', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.fundraising.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['campaign_id'] = 'FND' . str_pad(Fundraising::count() + 1, 6, '0', STR_PAD_LEFT);
        Fundraising::create($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Fundraising campaign created successfully');
    }

    public function show($id)
    {
        $fundraising = Fundraising::with(['contributions', 'expenses'])->findOrFail($id);
        return view('admin.fundraising.show', compact('fundraising'));
    }

    public function edit($id)
    {
        $fundraising = Fundraising::findOrFail($id);
        return view('admin.fundraising.edit', compact('fundraising'));
    }

    public function update(Request $request, $id)
    {
        $fundraising = Fundraising::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $fundraising->update($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Fundraising campaign updated successfully');
    }

    public function destroy($id)
    {
        $fundraising = Fundraising::findOrFail($id);
        $fundraising->delete();

        return redirect()->route('admin.fundraising.index')->with('success', 'Fundraising campaign deleted successfully');
    }
}
