<?php

namespace App\Http\Controllers\TD;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use App\Http\Controllers\Controller;
use App\Models\DashboardPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PhotoController extends Controller
{
    private static bool $cloudinaryConfigured = false;

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
            $storedPath = $this->storePhoto($file, $type);
            $generatedTitle = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
            $finalTitle = $baseTitle !== ''
                ? (count($files) > 1 ? $baseTitle . ' #' . ($index + 1) : $baseTitle)
                : Str::title(str_replace(['-', '_'], ' ', $generatedTitle));

            DashboardPhoto::create([
                'type' => $type,
                'photo_path' => $storedPath,
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

        $this->deletePhotoFile($photo->photo_path);
        
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

    private function storePhoto(UploadedFile $file, string $type): string
    {
        if ($this->isCloudinaryEnabled()) {
            try {
                $this->configureCloudinary();
                $result = (new UploadApi())->upload($file->getRealPath(), [
                    'folder' => 'bss/dashboard_photos/' . $type,
                    'resource_type' => 'image',
                    'overwrite' => true,
                ]);

                $url = (string) ($result['secure_url'] ?? $result['url'] ?? '');
                if ($url !== '') {
                    return $url;
                }
            } catch (Throwable $e) {
                Log::error('Cloudinary dashboard photo upload failed. Falling back to local disk.', [
                    'error' => $e->getMessage(),
                    'type' => $type,
                ]);
            }
        }

        $localPath = $file->store('dashboard-photos', 'public');
        return Storage::url($localPath);
    }

    private function deletePhotoFile(?string $photoPath): void
    {
        if (!$photoPath) {
            return;
        }

        if (filter_var($photoPath, FILTER_VALIDATE_URL) && $this->isCloudinaryEnabled()) {
            try {
                $this->configureCloudinary();
                $publicId = $this->extractCloudinaryPublicId($photoPath);
                if ($publicId) {
                    (new UploadApi())->destroy($publicId, [
                        'resource_type' => 'image',
                        'invalidate' => true,
                    ]);
                    return;
                }
            } catch (Throwable $e) {
                Log::warning('Failed to delete dashboard photo from Cloudinary.', [
                    'error' => $e->getMessage(),
                    'photo_path' => $photoPath,
                ]);
            }
        }

        $normalizedPath = ltrim(str_replace('/storage/', '', $photoPath), '/');
        if ($normalizedPath !== '') {
            Storage::disk('public')->delete($normalizedPath);
        }
    }

    private function isCloudinaryEnabled(): bool
    {
        return trim((string) env('CLOUDINARY_URL', '')) !== '';
    }

    private function configureCloudinary(): void
    {
        if (self::$cloudinaryConfigured) {
            return;
        }

        Configuration::instance(trim((string) env('CLOUDINARY_URL', '')));
        self::$cloudinaryConfigured = true;
    }

    private function extractCloudinaryPublicId(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $uploadIndex = array_search('upload', $segments, true);
        if ($uploadIndex === false) {
            return null;
        }

        $assetSegments = array_slice($segments, $uploadIndex + 1);
        if ($assetSegments === []) {
            return null;
        }

        $versionIndex = null;
        foreach ($assetSegments as $idx => $segment) {
            if (preg_match('/^v\d+$/', $segment)) {
                $versionIndex = $idx;
                break;
            }
        }

        if ($versionIndex !== null) {
            $assetSegments = array_slice($assetSegments, $versionIndex + 1);
        }

        if ($assetSegments === []) {
            return null;
        }

        $last = array_pop($assetSegments);
        $assetSegments[] = pathinfo($last, PATHINFO_FILENAME);

        return implode('/', $assetSegments);
    }
}
