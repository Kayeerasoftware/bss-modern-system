<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\DashboardPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $query = DashboardPhoto::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $photos = $query->orderBy('order')->orderBy('created_at', 'desc')->get();
        $photoTypes = $photos->pluck('type')->filter()->unique()->values();
        $projectPhotos = $photos->where('type', 'project');
        $meetingPhotos = $photos->where('type', 'meeting');
        
        return view('td.photos.index', compact('photos', 'projectPhotos', 'meetingPhotos', 'photoTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'custom_type' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120',
            'photos' => 'nullable|array|max:30',
            'photos.*' => 'image|max:5120',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $type = $this->normalizeType($request->input('type'), $request->input('custom_type'));

        $files = [];
        if ($request->hasFile('photo')) {
            $files[] = $request->file('photo');
        }
        if ($request->hasFile('photos')) {
            $files = array_merge($files, $request->file('photos'));
        }
        $files = array_values(array_filter($files, static fn ($file) => $file instanceof UploadedFile && $file->isValid()));

        if (empty($files)) {
            return redirect()->route('td.photos.index')->withErrors([
                'photo' => 'Upload at least one valid image.',
            ]);
        }

        $baseTitle = trim((string) $request->input('title', ''));
        $description = $request->input('description');
        $startOrder = (int) $request->input('order', 0);
        $uploadedCount = 0;

        foreach ($files as $index => $file) {
            $storedPath = $file->store('dashboard-photos', 'public');
            $generatedTitle = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
            $finalTitle = $baseTitle !== ''
                ? (count($files) > 1 ? $baseTitle . ' #' . ($index + 1) : $baseTitle)
                : Str::title(str_replace(['-', '_'], ' ', $generatedTitle));

            DashboardPhoto::create([
                'type' => $type,
                'photo_path' => Storage::url($storedPath),
                'title' => Str::limit($finalTitle, 255, ''),
                'description' => $description,
                'order' => $startOrder + $index,
                'is_active' => true,
            ]);
            $uploadedCount++;
        }

        $message = $uploadedCount === 1 ? 'Photo uploaded successfully.' : "{$uploadedCount} photos uploaded successfully.";
        return redirect()->route('td.photos.index')->with('success', $message);
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

    private function normalizeType(string $type, ?string $customType = null): string
    {
        $baseType = strtolower(trim($type));

        if ($baseType === 'other') {
            $custom = strtolower(trim((string) $customType));
            $slug = Str::slug($custom, '_');
            return $slug !== '' ? Str::limit($slug, 50, '') : 'other';
        }

        $slug = Str::slug($baseType, '_');
        return $slug !== '' ? Str::limit($slug, 50, '') : 'other';
    }
}
