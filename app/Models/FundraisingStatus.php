<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundraisingStatus extends Model
{
    protected $table = 'fundraising_statuses';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
    ];
}
