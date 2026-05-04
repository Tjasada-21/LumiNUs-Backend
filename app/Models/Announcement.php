<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Admin;
use App\Models\ImagesAnnouncement;
use App\Models\Comment;
use App\Models\Reaction;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';

    protected $fillable = [
        'admin_id',
        'announcement_title',
        'announcement_description',
        'date_posted',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'announcement_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'announcement_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ImagesAnnouncement::class, 'announcement_id');
    }
}
