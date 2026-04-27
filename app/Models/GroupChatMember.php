<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupChat;
use App\Models\Alumni;

class GroupChatMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_chat_id',
        'alumni_id',
        'role',
        'last_read_message_id',
    ];

    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class);
    }

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }
}