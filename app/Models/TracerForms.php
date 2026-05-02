<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}