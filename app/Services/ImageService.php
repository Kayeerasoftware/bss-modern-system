<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ProfilePictureStorageService;

class ImageService
{
    /**
     * Upload and process member profile picture
     */
    public function uploadMemberPicture(UploadedFile $file, ?string $oldPicture = null): string
    {
        if ((string) env('CLOUDINARY_URL', '') !== '') {
            return ProfilePictureStorageService::storeProfilePicture($file, $oldPicture, 'bss/profile_pictures/members');
        }

        if ($oldPicture) {
            $this->deletePicture($oldPicture);
        }

        $filename = $this->generateFilename($file);
        $path = 'profile-pictures/members/' . $filename;

        Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()), 'public');

        return $path;
    }

    /**
     * Delete profile picture
     */
    public function deletePicture(?string $picturePath): bool
    {
        if ((string) env('CLOUDINARY_URL', '') !== '') {
            return ProfilePictureStorageService::deleteProfilePicture($picturePath);
        }

        if (!$picturePath) {
            return false;
        }

        if (filter_var($picturePath, FILTER_VALIDATE_URL)) {
            return false;
        }

        return Storage::disk('s3')->delete($picturePath);
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return 'member_' . time() . '_' . Str::random(10) . '.' . $extension;
    }

    public function getPictureUrl(?string $picturePath, string $size = 'original'): string
    {
        if (!$picturePath) {
            return asset('images/default-avatar.svg');
        }

        if (filter_var($picturePath, FILTER_VALIDATE_URL)) {
            return $picturePath;
        }

        if (Storage::disk('s3')->exists($picturePath)) {
            return Storage::disk('s3')->url($picturePath);
        }

        return asset('images/default-avatar.svg');
    }

    /**
     * Validate image file
     */
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];

        // Check file size (5MB max)
        if ($file->getSize() > 5242880) {
            $errors[] = 'Image size must be less than 5MB';
        }

        // Check dimensions
        $imageSize = getimagesize($file->getRealPath());
        if ($imageSize) {
            $width = $imageSize[0];
            $height = $imageSize[1];
            
            if ($width < 100 || $height < 100) {
                $errors[] = 'Image must be at least 100x100 pixels';
            }
            
            if ($width > 2000 || $height > 2000) {
                $errors[] = 'Image must not exceed 2000x2000 pixels';
            }
        }

        return $errors;
    }

    /**
     * Get image info
     */
    public function getImageInfo(?string $picturePath): array
    {
        if (!$picturePath) {
            return ['exists' => false, 'url' => asset('images/default-avatar.svg'), 'size' => 0, 'dimensions' => null];
        }

        if (filter_var($picturePath, FILTER_VALIDATE_URL)) {
            return ['exists' => true, 'url' => $picturePath, 'size' => 0, 'dimensions' => null, 'thumbnail_url' => $picturePath, 'small_url' => $picturePath];
        }

        if (!Storage::disk('s3')->exists($picturePath)) {
            return ['exists' => false, 'url' => asset('images/default-avatar.svg'), 'size' => 0, 'dimensions' => null];
        }

        return [
            'exists' => true,
            'url' => Storage::disk('s3')->url($picturePath),
            'size' => Storage::disk('s3')->size($picturePath),
            'dimensions' => null,
            'thumbnail_url' => Storage::disk('s3')->url($picturePath),
            'small_url' => Storage::disk('s3')->url($picturePath),
        ];
    }
}
