<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Event;

class ImagesEvent extends Model
{
    use HasFactory;

    protected $table = 'images_events';

    protected $fillable = [
        'event_id',
        'image_path',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}