<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ImagesEvent;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'admin_id',
        'venue_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'max_capacity',
        'status',
        'event_type',
        'platform',
        'platform_url',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ImagesEvent::class, 'event_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}