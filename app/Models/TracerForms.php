<?php

namespace App\Models;

use App\Models\TracerQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TracerForms extends Model
{
    use HasFactory;

    protected $table = 'tracer_forms';

    protected $fillable = [
        'admin_id',
        'form_title',
        'form_description',
        'form_header',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function tracerQuestions(): HasMany
    {
        return $this->hasMany(TracerQuestion::class, 'form_id')->orderBy('order_priority');
    }
}