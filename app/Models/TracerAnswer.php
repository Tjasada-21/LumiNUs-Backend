<?php

namespace App\Models;

use App\Models\TracerQuestion;
use App\Models\TracerResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TracerAnswer extends Model
{
    use HasFactory;

    protected $table = 'tracer_answers';

    protected $fillable = [
        'tracer_response_id',
        'tq_id',
        'answer_value',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(TracerResponse::class, 'tracer_response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(TracerQuestion::class, 'tq_id');
    }
}
