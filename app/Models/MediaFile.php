<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaFile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * (Optional: Laravel assumes 'media_files' by default based on pluralization)
     */
    protected $table = 'media_files';

    /**
     * The attributes that are mass assignable.
     * This protects your app from malicious form submissions.
     */
    protected $fillable = [
        'original_name',
        'r2_path',
        'mime_type',
        'file_size',
    ];

    /**
     * Get the human-readable file size (Optional Helper Method).
     * Converts raw bytes from Aiven MySQL into KB, MB, or GB.
     */
    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

