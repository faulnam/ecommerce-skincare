<?php

namespace App\Http\Controllers;

use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    public function poll(Request $request, $sessionId)
    {
        $chat = LiveChat::where('session_id', $sessionId)->firstOrFail();
        
        $query = $chat->messages()->orderBy('id', 'asc');
        
        if ($request->has('last_id')) {
            $query->where('id', '>', $request->input('last_id'));
        }

        $messages = $query->get();

        return response()->json([
            'status' => $chat->status,
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->sender_type,
                    'text' => $msg->message,
                    'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    public function send(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $chat = LiveChat::where('session_id', $sessionId)->firstOrFail();

        if ($chat->status === 'closed') {
            return response()->json(['error' => 'Sesi obrolan sudah ditutup.'], 403);
        }

        $msg = LiveChatMessage::create([
            'live_chat_id' => $chat->id,
            'sender_type' => 'user',
            'message' => $request->input('message')
        ]);

        return response()->json([
            'id' => $msg->id,
            'sender' => $msg->sender_type,
            'text' => $msg->message,
            'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}
