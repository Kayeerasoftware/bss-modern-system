<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Member;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|string',
            'receiver_id' => 'required|string',
            'message' => 'required|string'
        ]);

        $message = ChatMessage::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'time' => $message->created_at->format('H:i')
        ]);
    }

    public function getMessages($senderId, $receiverId)
    {
        $messages = ChatMessage::where(function($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->orderBy('created_at', 'asc')->get();

        // Mark messages as read
        ChatMessage::where('sender_id', $receiverId)
            ->where('receiver_id', $senderId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(function($msg) use ($senderId) {
                return [
                    'id' => $msg->id,
                    'text' => $msg->message,
                    'sender' => $msg->sender_id === $senderId ? 'me' : 'member',
                    'time' => $msg->created_at->format('H:i'),
                    'is_read' => $msg->is_read
                ];
            })
        ]);
    }

    public function getConversations($memberId)
    {
        $conversations = ChatMessage::where('sender_id', $memberId)
            ->orWhere('receiver_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($msg) use ($memberId) {
                return $msg->sender_id === $memberId ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function($messages, $otherMemberId) use ($memberId) {
                $lastMessage = $messages->first();
                $unreadCount = $messages->where('receiver_id', $memberId)
                    ->where('is_read', false)
                    ->count();
                
                $member = Member::where('member_id', $otherMemberId)->first();
                
                return [
                    'member_id' => $otherMemberId,
                    'full_name' => $member->full_name ?? 'Unknown',
                    'role' => $member->role ?? 'member',
                    'last_message' => $lastMessage->message,
                    'last_time' => $lastMessage->created_at->format('H:i'),
                    'unread' => $unreadCount
                ];
            })->values();

        return response()->json(['conversations' => $conversations]);
    }

    public function markAsRead(Request $request)
    {
        ChatMessage::where('sender_id', $request->sender_id)
            ->where('receiver_id', $request->receiver_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
