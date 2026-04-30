<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniEmployment extends Model
{
    use HasFactory;

    protected $table = 'alumni_employments';

    protected $fillable = [
        'alumni_id',
        'job_title',
        'company',
        'location',
        'career_description',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }
}
