<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Allow these columns to be mass-assigned when saving to the database
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'is_read'
    ];

    // Define the relationship: A message belongs to a sender (Alumni)
    public function sender()
    {
        return $this->belongsTo(Alumni::class, 'sender_id');
    }

    // Define the relationship: A message belongs to a receiver (Alumni)
    public function receiver()
    {
        return $this->belongsTo(Alumni::class, 'receiver_id');
    }
}