<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;

class MeetingController extends Controller
{
    public function index()
    {
        return response()->json(Meeting::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'scheduled_at' => 'required|date',
            'location' => 'required|string|max:255'
        ]);

        $meeting = Meeting::create([
            ...$validated,
            'status' => 'scheduled',
            'created_by' => auth()->user()->id ?? 'system'
        ]);

        return response()->json(['success' => true, 'meeting' => $meeting]);
    }

    public function show($id)
    {
        return response()->json(Meeting::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->update($request->all());
        return response()->json(['success' => true, 'meeting' => $meeting]);
    }

    public function destroy($id)
    {
        Meeting::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}