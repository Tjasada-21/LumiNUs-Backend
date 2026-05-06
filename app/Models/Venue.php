<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
