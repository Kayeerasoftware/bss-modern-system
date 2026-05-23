<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareStatus extends Model
{
    protected $table = 'share_statuses';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
    ];
}
