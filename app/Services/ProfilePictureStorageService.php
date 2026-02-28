<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProfilePictureStorageService
{
    private static bool $configured = false;

    public static function storeProfilePicture(UploadedFile $file, ?string $oldPath = null, string $folder = 'bss/profile_pictures'): string
    {
        if (self::isCloudinaryEnabled()) {
            try {
                self::configureCloudinary();
                if ($oldPath) {
                    self::deleteProfilePicture($oldPath);
                }

                $result = (new UploadApi())->upload($file->getRealPath(), [
                    'folder' => $folder,
                    'resource_type' => 'image',
                    'overwrite' => true,
                ]);

                $url = (string) ($result['secure_url'] ?? $result['url'] ?? '');
                if ($url === '') {
                    throw new RuntimeException('Cloudinary upload returned empty URL.');
                }

                return $url;
            } catch (Throwable $e) {
                Log::error('Cloudinary upload failed, falling back to local public disk.', [
                    'error' => $e->getMessage(),
                    'folder' => $folder,
                ]);
            }
        }

        if ($oldPath) {
            self::deleteProfilePicture($oldPath);
        }

        return $file->store('profile_pictures', 'public');
    }

    public static function deleteProfilePicture(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (self::isCloudinaryEnabled() && filter_var($path, FILTER_VALIDATE_URL)) {
            try {
                self::configureCloudinary();
                $publicId = self::extractCloudinaryPublicId($path);
                if ($publicId) {
                    (new UploadApi())->destroy($publicId, [
                        'resource_type' => 'image',
                        'invalidate' => true,
                    ]);
                    return true;
                }
            } catch (Throwable $e) {
                Log::warning('Cloudinary delete failed for profile picture.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return Storage::disk('public')->delete($path);
    }

    public static function publicUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    private static function isCloudinaryEnabled(): bool
    {
        return (string) env('CLOUDINARY_URL', '') !== '';
    }

    private static function configureCloudinary(): void
    {
        if (self::$configured) {
            return;
        }

        $cloudinaryUrl = trim((string) env('CLOUDINARY_URL', ''));
        if ($cloudinaryUrl === '') {
            throw new RuntimeException('CLOUDINARY_URL is empty.');
        }

        // Common copy/paste mistake from docs/examples.
        if (str_contains($cloudinaryUrl, '<') || str_contains($cloudinaryUrl, '>')) {
            throw new RuntimeException('CLOUDINARY_URL must not contain angle brackets.');
        }

        Configuration::instance($cloudinaryUrl);
        self::$configured = true;
    }

    private static function extractCloudinaryPublicId(string $url): ?string
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

        // Skip transformation segments until the version segment.
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
        $last = pathinfo($last, PATHINFO_FILENAME);
        $assetSegments[] = $last;

        return implode('/', $assetSegments);
    }
}
