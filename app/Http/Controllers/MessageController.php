<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;

        $unreadCount = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    // 1. Fetch the entire chat history between the logged-in user and a specific contact
    public function fetchThread(Request $request, $contactId)
    {
        $userId = $request->user()->id; // The currently logged-in alumni

        $messages = Message::with('sender')
            ->where(function ($query) use ($userId, $contactId) {
                // Messages I sent to them
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $contactId);
            })
            ->orWhere(function ($query) use ($userId, $contactId) {
                // Messages they sent to me
                $query->where('sender_id', $contactId)
                      ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc') // Oldest at the top, newest at the bottom
            ->get();

        return response()->json(['messages' => $messages]);
    }

    // 2. Save a new message to the database
    public function sendMessage(Request $request, $contactId)
    {
        // Validate that the user actually typed something
        $request->validate([
            'content' => 'required|string',
        ]);

        $userId = $request->user()->id;

        // Save it to your database
        $message = Message::create([
            'sender_id' => $userId,
            'receiver_id' => $contactId,
            'content' => $request->content,
            'is_read' => false, // Default to unread
        ]);

        // Load the sender info just in case the frontend needs the avatar/name
        $message->load('sender'); 

        // The absolute millisecond this saves, Supabase Realtime will automatically 
        // shoot it down the WebSocket tunnel to the other user!
        return response()->json($message, 201);
    }

    // 3. Mark messages as read when the user opens the chat screen
    public function markAsRead(Request $request, $contactId)
    {
        $userId = $request->user()->id;

        // Find all unread messages sent by the contact TO me, and mark them read
        Message::where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}