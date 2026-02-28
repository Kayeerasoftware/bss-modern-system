<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * Upload and process member profile picture
     */
    public function uploadMemberPicture(UploadedFile $file, ?string $oldPicture = null): string
    {
        // Delete old picture if exists
        if ($oldPicture) {
            $this->deletePicture($oldPicture);
        }

        // Generate unique filename
        $filename = $this->generateFilename($file);
        
        // Process and save image
        $this->processAndSaveImage($file, $filename);
        
        return 'profile-pictures/members/' . $filename;
    }

    /**
     * Delete profile picture
     */
    public function deletePicture(?string $picturePath): bool
    {
        if (!$picturePath) {
            return false;
        }

        return Storage::disk('public')->delete($picturePath);
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return 'member_' . time() . '_' . Str::random(10) . '.' . $extension;
    }

    /**
     * Process and save image with multiple sizes
     */
    private function processAndSaveImage(UploadedFile $file, string $filename): void
    {
        $disk = Storage::disk('public');
        $basePath = 'profile-pictures/members/';

        // Original image (resized to max 800x800)
        $image = Image::make($file->getRealPath());
        $image->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $disk->put($basePath . $filename, (string) $image->encode(null, 85), 'public');

        // Thumbnail (150x150)
        $thumbnailName = 'thumb_' . $filename;
        $thumbnail = Image::make($file->getRealPath());
        $thumbnail->fit(150, 150);
        $disk->put($basePath . $thumbnailName, (string) $thumbnail->encode(null, 80), 'public');

        // Small size (300x300)
        $smallName = 'small_' . $filename;
        $small = Image::make($file->getRealPath());
        $small->fit(300, 300);
        $disk->put($basePath . $smallName, (string) $small->encode(null, 80), 'public');
    }

    /**
     * Get picture URL with size option
     */
    public function getPictureUrl(?string $picturePath, string $size = 'original'): string
    {
        if (!$picturePath) {
            return asset('images/default-avatar.svg');
        }

        $pathInfo = pathinfo($picturePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        switch ($size) {
            case 'thumbnail':
                $sizedPath = $directory . '/thumb_' . $filename . '.' . $extension;
                break;
            case 'small':
                $sizedPath = $directory . '/small_' . $filename . '.' . $extension;
                break;
            default:
                $sizedPath = $picturePath;
        }

        // Check if sized version exists, fallback to original
        if (Storage::disk('public')->exists($sizedPath)) {
            return Storage::disk('public')->url($sizedPath);
        }

        return Storage::disk('public')->exists($picturePath) 
            ? Storage::disk('public')->url($picturePath)
            : asset('images/default-avatar.svg');
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
        if (!$picturePath || !Storage::disk('public')->exists($picturePath)) {
            return [
                'exists' => false,
                'url' => asset('images/default-avatar.svg'),
                'size' => 0,
                'dimensions' => null
            ];
        }

        $imageSize = null;
        try {
            $fullPath = Storage::disk('public')->path($picturePath);
            $imageSize = @getimagesize($fullPath) ?: null;
        } catch (\Throwable $e) {
            // Not all disks support local file paths (e.g. S3).
            $imageSize = null;
        }
        
        return [
            'exists' => true,
            'url' => Storage::disk('public')->url($picturePath),
            'size' => Storage::disk('public')->size($picturePath),
            'dimensions' => $imageSize ? ['width' => $imageSize[0], 'height' => $imageSize[1]] : null,
            'thumbnail_url' => $this->getPictureUrl($picturePath, 'thumbnail'),
            'small_url' => $this->getPictureUrl($picturePath, 'small')
        ];
    }
}
