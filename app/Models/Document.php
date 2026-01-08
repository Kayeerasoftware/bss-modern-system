<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'filename', 'file_path', 'file_type', 'file_size',
        'category', 'description', 'uploaded_by', 'access_roles'
    ];

    protected $casts = [
        'access_roles' => 'array'
    ];

    public function uploader()
    {
        return $this->belongsTo(Member::class, 'uploaded_by', 'member_id');
    }
}