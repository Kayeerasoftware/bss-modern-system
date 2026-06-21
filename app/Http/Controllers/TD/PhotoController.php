<?php

namespace App\Http\Controllers\TD;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use App\Http\Controllers\Controller;
use App\Models\DashboardPhoto;
use App\Services\ProfilePictureStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PhotoController extends Controller
{
    private static bool $cloudinaryConfigured = false;

    private const VALID_TYPES = ['project', 'meeting', 'event', 'achievement', 'slider'];

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

        $photos = $query->orderBy('display_order')->orderBy('created_at', 'desc')->get();
        $photoTypes = $photos->pluck('type')->filter()->unique()->values();
        $projectPhotos = $photos->where('type', 'project');
        $meetingPhotos = $photos->where('type', 'meeting');

        return view('td.photos.index', compact('photos', 'projectPhotos', 'meetingPhotos', 'photoTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'          => 'required|string|max:50',
            'custom_type'   => 'nullable|string|max:50',
            'photo'         => 'nullable|image|max:5120',
            'photos'        => 'nullable|array|max:30',
            'photos.*'      => 'image|max:5120',
            'title'         => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'order'         => 'nullable|integer|min:0',
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

        $baseTitle  = trim((string) $request->input('title', ''));
        $description = $request->input('description');
        $startOrder  = (int) $request->input('display_order', $request->input('order', 0));
        $uploadedCount = 0;

        foreach ($files as $index => $file) {
            $storedPath = $this->storePhoto($file, $type);
            $generatedTitle = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
            $finalTitle = $baseTitle !== ''
                ? (count($files) > 1 ? $baseTitle . ' #' . ($index + 1) : $baseTitle)
                : Str::title(str_replace(['-', '_'], ' ', $generatedTitle));

            DashboardPhoto::create([
                'photo_number'  => 'PHO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'type'          => in_array($type, self::VALID_TYPES, true) ? $type : 'event',
                'photo_path'    => $storedPath,
                'title'         => Str::limit($finalTitle, 255, ''),
                'description'   => $description,
                'display_order' => $startOrder + $index,
                'is_active'     => true,
                'uploaded_by'   => auth()->id(),
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
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'display_order' => 'nullable|integer',
            'order'         => 'nullable|integer',
            'is_active'     => 'boolean',
        ]);

        $updateData = $request->only(['title', 'description', 'display_order', 'is_active']);
        if ($request->filled('order') && !$request->filled('display_order')) {
            $updateData['display_order'] = $request->input('order');
        }

        $photo->update($updateData);

        return redirect()->route('td.photos.index')->with('success', 'Photo updated successfully');
    }

    public function destroy($id)
    {
        $photo = DashboardPhoto::findOrFail($id);
        $this->deletePhotoFile($photo->photo_path);
        $photo->delete();

        return redirect()->route('td.photos.index')->with('success', 'Photo deleted successfully');
    }

    public function batchDestroy(Request $request)
    {
        $validated = $request->validate([
            'photo_ids'   => 'required|array|min:1',
            'photo_ids.*' => 'integer|exists:dashboard_photos,id',
        ]);

        $photos = DashboardPhoto::query()->whereIn('id', $validated['photo_ids'])->get();

        foreach ($photos as $photo) {
            $this->deletePhotoFile($photo->photo_path);
            $photo->delete();
        }

        $count = $photos->count();
        $message = $count === 1 ? '1 photo deleted successfully.' : "{$count} photos deleted successfully.";

        return redirect()->route('td.photos.index')->with('success', $message);
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
            return $slug !== '' ? Str::limit($slug, 50, '') : 'event';
        }

        // Map to valid ENUM values
        if (in_array($baseType, self::VALID_TYPES, true)) {
            return $baseType;
        }

        return 'event';
    }

    private function storePhoto(UploadedFile $file, string $type): string
    {
        if ($this->isCloudinaryEnabled()) {
            try {
                $this->configureCloudinary();
                $result = (new UploadApi())->upload($file->getRealPath(), [
                    'folder'        => 'bss/dashboard_photos/' . $type,
                    'resource_type' => 'image',
                    'overwrite'     => true,
                ]);

                $url = (string) ($result['secure_url'] ?? $result['url'] ?? '');
                if ($url !== '') {
                    return $url;
                }
            } catch (Throwable $e) {
                Log::error('Cloudinary dashboard photo upload failed. Falling back to R2.', [
                    'error' => $e->getMessage(),
                    'type'  => $type,
                ]);
            }
        }

        $path = 'dashboard-photos/' . $type . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()), 'public');

        return ProfilePictureStorageService::buildPublicUrl($path);
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
                        'invalidate'    => true,
                    ]);
                    return;
                }
            } catch (Throwable $e) {
                Log::warning('Failed to delete dashboard photo from Cloudinary.', [
                    'error'      => $e->getMessage(),
                    'photo_path' => $photoPath,
                ]);
            }
        }

        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            // R2 full URL — extract relative path and delete from s3
            $r2Base = rtrim((string) env('R2_PUBLIC_URL', env('AWS_URL', '')), '/');
            if ($r2Base !== '' && str_starts_with($photoPath, $r2Base)) {
                $relativePath = ltrim(substr($photoPath, strlen($r2Base)), '/');
                Storage::disk('s3')->delete($relativePath);
            }
            return;
        }

        Storage::disk('s3')->delete($photoPath);
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
