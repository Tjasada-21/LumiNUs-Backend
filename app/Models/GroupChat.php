<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupMessage;

class GroupChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar_url',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(Alumni::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(Alumni::class, 'group_chat_members', 'group_chat_id', 'alumni_id')
            ->withPivot('last_read_message_id', 'role')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(GroupMessage::class)->latestOfMany();
    }
}