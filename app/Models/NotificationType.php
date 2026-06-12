<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $table = 'notification_types';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'color',
    ];
}
