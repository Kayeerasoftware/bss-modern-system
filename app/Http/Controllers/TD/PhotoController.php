<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\DashboardPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = DashboardPhoto::orderBy('order')->orderBy('created_at', 'desc')->get();
        $projectPhotos = $photos->where('type', 'project');
        $meetingPhotos = $photos->where('type', 'meeting');
        
        return view('td.photos.index', compact('photos', 'projectPhotos', 'meetingPhotos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:project,meeting',
            'photo' => 'required|image|max:5120',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $path = $request->file('photo')->store('dashboard-photos', 'public');

        DashboardPhoto::create([
            'type' => $request->type,
            'photo_path' => Storage::url($path),
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => true
        ]);

        return redirect()->route('td.photos.index')->with('success', 'Photo uploaded successfully');
    }

    public function update(Request $request, $id)
    {
        $photo = DashboardPhoto::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $photo->update($request->only(['title', 'description', 'order', 'is_active']));

        return redirect()->route('td.photos.index')->with('success', 'Photo updated successfully');
    }

    public function destroy($id)
    {
        $photo = DashboardPhoto::findOrFail($id);
        
        if ($photo->photo_path && Storage::disk('public')->exists(str_replace('/storage/', '', $photo->photo_path))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $photo->photo_path));
        }
        
        $photo->delete();

        return redirect()->route('td.photos.index')->with('success', 'Photo deleted successfully');
    }

    public function toggleStatus($id)
    {
        $photo = DashboardPhoto::findOrFail($id);
        $photo->update(['is_active' => !$photo->is_active]);

        return response()->json(['success' => true, 'is_active' => $photo->is_active]);
    }
}
