<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function index()
    {
        return view('admin.chats.index');
    }

    public function getChats()
    {
        // Get waiting and active chats
        $chats = LiveChat::whereIn('status', ['waiting', 'active'])
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($chats);
    }

    public function getMessages(Request $request, $chatId)
    {
        $chat = LiveChat::findOrFail($chatId);
        
        if ($chat->status === 'waiting') {
            $chat->status = 'active';
            $chat->save();
        }

        $query = $chat->messages()->orderBy('id', 'asc');
        
        if ($request->has('last_id')) {
            $query->where('id', '>', $request->input('last_id'));
        }

        $messages = $query->get();

        return response()->json([
            'status' => $chat->status,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $chat = LiveChat::findOrFail($chatId);

        if ($chat->status === 'closed') {
            return response()->json(['error' => 'Chat is closed'], 400);
        }

        if ($chat->status === 'waiting') {
            $chat->status = 'active';
        }
        $chat->touch(); // Update updated_at

        $msg = LiveChatMessage::create([
            'live_chat_id' => $chat->id,
            'sender_type' => 'admin',
            'message' => $request->input('message')
        ]);

        return response()->json($msg);
    }

    public function closeChat($chatId)
    {
        $chat = LiveChat::findOrFail($chatId);
        $chat->status = 'closed';
        $chat->save();

        return response()->json(['success' => true]);
    }

    public function countWaiting()
    {
        $count = LiveChat::where('status', 'waiting')->count();
        return response()->json(['count' => $count]);
    }
}
