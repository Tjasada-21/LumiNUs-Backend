<?php

namespace App\Models;

use App\Models\TracerAnswerOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TracerQuestion extends Model
{
    use HasFactory;

    protected $table = 'tracer_questions';

    protected $fillable = [
        'form_id',
        'type',
        'question_text',
        'description',
        'is_required',
        'order_priority',
        'settings',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'settings' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(TracerForms::class, 'form_id');
    }

    public function answerOptions(): HasMany
    {
        return $this->hasMany(TracerAnswerOption::class, 'tq_id');
    }
}
