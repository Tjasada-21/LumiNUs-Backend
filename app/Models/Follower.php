<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    // 1. Define your statuses here so you never have to memorize the numbers!
    public const STATUS_PENDING = 0;
    public const STATUS_CONNECTED = 1;
    public const STATUS_DELETED = 2;

    // 2. Add status to your fillable array
    protected $fillable = [
        'follower_alumni_id',
        'followed_alumni_id',
        'status',
    ];

    // 3. Make sure Laravel always treats it as a number
    protected $casts = [
        'status' => 'integer',
    ];
}