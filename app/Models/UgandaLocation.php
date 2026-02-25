<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UgandaLocation extends Model
{
    protected $fillable = ['name', 'type', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(UgandaLocation::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(UgandaLocation::class, 'parent_id');
    }
}
