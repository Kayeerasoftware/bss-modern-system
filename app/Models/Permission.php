<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'category'];

    public function roles()
    {
        return $this->belongsToMany(RolePermission::class);
    }
}
