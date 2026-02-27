<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    private function conversationCacheKey(string $memberId): string
    {
        return 'chat:conversations:'.$memberId;
    }

    private function unreadCacheKey(string $memberId): string
    {
        return 'chat:unread:'.$memberId;
    }

    private function invalidateChatCaches(array $memberIds): void
    {
        foreach (array_unique(array_filter($memberIds)) as $memberId) {
            Cache::forget($this->conversationCacheKey((string) $memberId));
            Cache::forget($this->unreadCacheKey((string) $memberId));
        }
    }

    protected function currentMemberId(): ?string
    {
        $user = Auth::user();
        return $user?->member?->member_id;
    }

    public function me()
    {
        $memberId = $this->currentMemberId();

        if (!$memberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        return response()->json([
            'success' => true,
            'member_id' => $memberId,
            'full_name' => Auth::user()->member->full_name,
            'role' => Auth::user()->role,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|string',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:20480'
        ]);

        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $receiver = Member::where('member_id', $request->receiver_id)->first();
        if (!$receiver) {
            return response()->json(['success' => false, 'message' => 'Receiver not found'], 404);
        }

        $cleanMessage = trim((string) $request->message);
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat-attachments', 'public');
        }

        if ($cleanMessage === '' && !$attachmentPath) {
            return response()->json(['success' => false, 'message' => 'Message or attachment is required'], 422);
        }

        if ($request->receiver_id === $currentMemberId) {
            return response()->json(['success' => false, 'message' => 'You cannot message yourself'], 422);
        }

        $message = ChatMessage::create([
            'sender_id' => $currentMemberId,
            'receiver_id' => $request->receiver_id,
            'message' => $cleanMessage !== '' ? $cleanMessage : '',
            'attachment' => $attachmentPath,
            'is_read' => false
        ]);

        $this->invalidateChatCaches([$currentMemberId, $request->receiver_id]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'text' => $message->message,
                'sender' => 'me',
                'time' => $message->created_at->format('H:i'),
                'timestamp' => $message->created_at->timestamp * 1000,
                'status' => 'sent',
                'is_read' => $message->is_read,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'attachment' => $message->attachment,
                'attachment_url' => $message->attachment ? Storage::url($message->attachment) : null,
                'attachment_name' => $message->attachment ? basename($message->attachment) : null,
            ]
        ]);
    }

    public function getMessagesWithMember($otherMemberId)
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        if ($otherMemberId === $currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Invalid conversation'], 422);
        }

        $otherMember = Member::where('member_id', $otherMemberId)->first();
        if (!$otherMember) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $messages = ChatMessage::where(function ($query) use ($currentMemberId, $otherMemberId) {
            $query->where('sender_id', $currentMemberId)->where('receiver_id', $otherMemberId);
        })->orWhere(function ($query) use ($currentMemberId, $otherMemberId) {
            $query->where('sender_id', $otherMemberId)->where('receiver_id', $currentMemberId);
        })->orderBy('created_at', 'asc')->get();

        ChatMessage::where('sender_id', $otherMemberId)
            ->where('receiver_id', $currentMemberId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->invalidateChatCaches([$currentMemberId, $otherMemberId]);

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($msg) use ($currentMemberId) {
                return [
                    'id' => $msg->id,
                    'text' => $msg->message,
                    'sender' => $msg->sender_id === $currentMemberId ? 'me' : 'them',
                    'time' => $msg->created_at->format('H:i'),
                    'timestamp' => $msg->created_at->timestamp * 1000,
                    'status' => $msg->sender_id === $currentMemberId
                        ? ($msg->is_read ? 'read' : 'delivered')
                        : 'delivered',
                    'is_read' => $msg->is_read,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'attachment' => $msg->attachment,
                    'attachment_url' => $msg->attachment ? Storage::url($msg->attachment) : null,
                    'attachment_name' => $msg->attachment ? basename($msg->attachment) : null,
                ];
            })
        ]);
    }

    public function getMessages($senderId, $receiverId)
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        if ($senderId === $currentMemberId) {
            return $this->getMessagesWithMember($receiverId);
        }

        if ($receiverId === $currentMemberId) {
            return $this->getMessagesWithMember($senderId);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized conversation access'], 403);
    }

    public function getConversations($memberId = null)
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        if ($memberId !== null && $memberId !== $currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized conversation access'], 403);
        }

        $conversations = Cache::remember($this->conversationCacheKey($currentMemberId), now()->addSeconds(5), function () use ($currentMemberId) {
            $conversationHeads = ChatMessage::query()
                ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_member_id, MAX(id) as last_id', [$currentMemberId])
                ->where(function ($query) use ($currentMemberId) {
                    $query->where('sender_id', $currentMemberId)
                        ->orWhere('receiver_id', $currentMemberId);
                })
                ->groupBy('other_member_id')
                ->orderByDesc(DB::raw('MAX(created_at)'))
                ->get();

            if ($conversationHeads->isEmpty()) {
                return collect();
            }

            $lastMessages = ChatMessage::query()
                ->whereIn('id', $conversationHeads->pluck('last_id')->all())
                ->get()
                ->keyBy('id');

            $unreadBySender = ChatMessage::query()
                ->selectRaw('sender_id as other_member_id, COUNT(*) as unread_count')
                ->where('receiver_id', $currentMemberId)
                ->where('is_read', false)
                ->groupBy('sender_id')
                ->pluck('unread_count', 'other_member_id');

            $members = Member::query()
                ->with('user')
                ->whereIn('member_id', $conversationHeads->pluck('other_member_id')->all())
                ->get()
                ->keyBy('member_id');

            return $conversationHeads->map(function ($row) use ($lastMessages, $unreadBySender, $members) {
                $otherMemberId = (string) $row->other_member_id;
                $lastMessage = $lastMessages->get($row->last_id);
                $member = $members->get($otherMemberId);

                return [
                    'member_id' => $otherMemberId,
                    'full_name' => $member?->full_name ?? 'Unknown',
                    'role' => $member?->user?->role ?? 'client',
                    'profile_picture' => $member?->profile_picture_url,
                    'last_message' => $lastMessage?->message ?? '',
                    'last_time' => $lastMessage?->created_at?->format('H:i'),
                    'timestamp' => $lastMessage?->created_at ? $lastMessage->created_at->timestamp * 1000 : null,
                    'unread' => (int) ($unreadBySender[$otherMemberId] ?? 0),
                ];
            })->values();
        });

        return response()->json(['success' => true, 'conversations' => $conversations]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'nullable|string',
            'receiver_id' => 'nullable|string',
            'member_id' => 'nullable|string',
            'other_member_id' => 'nullable|string',
        ]);

        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $otherMemberId = $request->other_member_id
            ?? $request->member_id
            ?? $request->sender_id;

        if (!$otherMemberId) {
            return response()->json(['success' => false, 'message' => 'Member is required'], 422);
        }

        ChatMessage::where('sender_id', $otherMemberId)
            ->where('receiver_id', $currentMemberId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->invalidateChatCaches([$currentMemberId, $otherMemberId]);

        return response()->json(['success' => true]);
    }

    public function markConversationAsRead($otherMemberId)
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        ChatMessage::where('sender_id', $otherMemberId)
            ->where('receiver_id', $currentMemberId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->invalidateChatCaches([$currentMemberId, $otherMemberId]);

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $count = Cache::remember($this->unreadCacheKey($currentMemberId), now()->addSeconds(5), function () use ($currentMemberId) {
            return ChatMessage::query()
                ->where('receiver_id', $currentMemberId)
                ->where('is_read', false)
                ->count();
        });

        return response()->json(['success' => true, 'unread' => $count]);
    }
}
