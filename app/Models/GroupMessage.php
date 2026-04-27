<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupChat;
use App\Models\Alumni;

class GroupMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_chat_id',
        'sender_id',
        'content',
        'reactions',
    ];

    protected $casts = [
        'reactions' => 'array',
    ];

    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class);
    }

    public function sender()
    {
        return $this->belongsTo(Alumni::class, 'sender_id');
    }
}