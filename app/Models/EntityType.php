<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    protected $table = 'entity_types';

    protected $fillable = [
        'name',
        'display_name',
        'table_name',
    ];
}
