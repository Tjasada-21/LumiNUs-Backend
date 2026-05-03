<?php

namespace App\Models;

use App\Models\TracerAnswer;
use App\Models\TracerForms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TracerResponse extends Model
{
    use HasFactory;

    protected $table = 'tracer_responses';

    protected $fillable = [
        'alumni_id',
        'form_id',
        'submitted_at',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(TracerForms::class, 'form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TracerAnswer::class, 'tracer_response_id');
    }
}
