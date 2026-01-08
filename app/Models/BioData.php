<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BioData extends Model
{
    use HasFactory;

    protected $table = 'bio_data';

    protected $fillable = [
        'full_name', 'nin_no', 'present_address', 'permanent_address',
        'telephone', 'email', 'dob', 'nationality', 'birth_place',
        'marital_status', 'spouse_name', 'spouse_nin', 'next_of_kin',
        'next_of_kin_nin', 'father_name', 'mother_name', 'children',
        'occupation', 'signature', 'declaration_date'
    ];

    protected $casts = [
        'present_address' => 'array',
        'permanent_address' => 'array',
        'telephone' => 'array',
        'birth_place' => 'array',
        'children' => 'array',
        'declaration_date' => 'date'
    ];
}