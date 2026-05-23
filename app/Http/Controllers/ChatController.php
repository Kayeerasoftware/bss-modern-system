<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
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

    protected function currentMemberId(): ?int
    {
        $user = Auth::user();
        return $user?->member?->id;
    }

    public function me()
    {
        $memberId = $this->currentMemberId();

        if (!$memberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $member = Auth::user()->member;

        return response()->json([
            'success' => true,
            'member_id' => $member->member_number,
            'full_name' => $member->full_name,
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

        $receiverId = resolve_member_id($request->receiver_id) ?? null;
        $receiver = $receiverId ? Member::find($receiverId) : null;
        if (!$receiver) {
            return response()->json(['success' => false, 'message' => 'Receiver not found'], 404);
        }

        if ($receiverId === $currentMemberId) {
            return response()->json(['success' => false, 'message' => 'You cannot message yourself'], 422);
        }

        $cleanMessage = trim((string) $request->message);
        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = $file->getClientMimeType();
            $attachmentSize = $file->getSize();
        }

        if ($cleanMessage === '' && !$attachmentPath) {
            return response()->json(['success' => false, 'message' => 'Message or attachment is required'], 422);
        }

        $conversation = $this->getOrCreateConversation($currentMemberId, $receiverId);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $currentMemberId,
            'message' => $cleanMessage !== '' ? $cleanMessage : '',
            'message_type' => $attachmentPath ? 'file' : 'text',
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'attachment_size' => $attachmentSize,
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $this->invalidateChatCaches([$currentMemberId, $receiverId]);

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
                'receiver_id' => $receiverId,
                'attachment' => $message->attachment_path,
                'attachment_url' => $message->attachment_path ? Storage::url($message->attachment_path) : null,
                'attachment_name' => $message->attachment_name,
            ]
        ]);
    }

    public function getMessagesWithMember($otherMemberId)
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $resolvedOtherId = resolve_member_id($otherMemberId) ?? null;
        if (!$resolvedOtherId || $resolvedOtherId === $currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Invalid conversation'], 422);
        }

        $otherMember = Member::find($resolvedOtherId);
        if (!$otherMember) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $conversation = $this->findConversation($currentMemberId, $resolvedOtherId);
        if (!$conversation) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        ChatMessage::where('conversation_id', $conversation->id)
            ->where('sender_id', $resolvedOtherId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $this->invalidateChatCaches([$currentMemberId, $resolvedOtherId]);

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($msg) use ($currentMemberId, $resolvedOtherId) {
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
                    'receiver_id' => $resolvedOtherId,
                    'attachment' => $msg->attachment_path,
                    'attachment_url' => $msg->attachment_path ? Storage::url($msg->attachment_path) : null,
                    'attachment_name' => $msg->attachment_name,
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

        $resolvedSender = resolve_member_id($senderId);
        $resolvedReceiver = resolve_member_id($receiverId);

        if ($resolvedSender === $currentMemberId) {
            return $this->getMessagesWithMember($receiverId);
        }

        if ($resolvedReceiver === $currentMemberId) {
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

        if ($memberId !== null && resolve_member_id($memberId) !== $currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized conversation access'], 403);
        }

        $conversations = Cache::remember($this->conversationCacheKey((string) $currentMemberId), now()->addSeconds(5), function () use ($currentMemberId) {
            $conversationIds = ChatParticipant::query()
                ->where('member_id', $currentMemberId)
                ->pluck('conversation_id')
                ->all();

            if (empty($conversationIds)) {
                return collect();
            }

            $lastMessages = ChatMessage::query()
                ->select('conversation_id', DB::raw('MAX(id) as last_id'))
                ->whereIn('conversation_id', $conversationIds)
                ->groupBy('conversation_id')
                ->get()
                ->keyBy('conversation_id');

            $messageMap = ChatMessage::whereIn('id', $lastMessages->pluck('last_id')->all())
                ->get()
                ->keyBy('id');

            $otherParticipants = ChatParticipant::query()
                ->whereIn('conversation_id', $conversationIds)
                ->where('member_id', '!=', $currentMemberId)
                ->with('member.user')
                ->get()
                ->groupBy('conversation_id');

            $unreadCounts = ChatMessage::query()
                ->selectRaw('conversation_id, COUNT(*) as unread_count')
                ->whereIn('conversation_id', $conversationIds)
                ->where('sender_id', '!=', $currentMemberId)
                ->where('is_read', false)
                ->groupBy('conversation_id')
                ->pluck('unread_count', 'conversation_id');

            return collect($conversationIds)->map(function ($conversationId) use ($lastMessages, $messageMap, $otherParticipants, $unreadCounts) {
                $lastMessageId = $lastMessages->get($conversationId)?->last_id;
                $lastMessage = $lastMessageId ? $messageMap->get($lastMessageId) : null;
                $member = $otherParticipants->get($conversationId)?->first()?->member;

                return [
                    'member_id' => $member?->member_number,
                    'full_name' => $member?->full_name ?? 'Unknown',
                    'role' => $member?->user?->role ?? 'client',
                    'profile_picture' => $member?->profile_picture_url,
                    'last_message' => $lastMessage?->message ?? '',
                    'last_time' => $lastMessage?->created_at?->format('H:i'),
                    'timestamp' => $lastMessage?->created_at ? $lastMessage->created_at->timestamp * 1000 : null,
                    'unread' => (int) ($unreadCounts[$conversationId] ?? 0),
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

        $resolvedOtherId = $otherMemberId ? resolve_member_id($otherMemberId) : null;

        if (!$resolvedOtherId) {
            return response()->json(['success' => false, 'message' => 'Member is required'], 422);
        }

        $conversation = $this->findConversation($currentMemberId, $resolvedOtherId);
        if ($conversation) {
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_id', $resolvedOtherId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        $this->invalidateChatCaches([$currentMemberId, $resolvedOtherId]);

        return response()->json(['success' => true]);
    }

    public function markConversationAsRead($otherMemberId)
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $resolvedOtherId = resolve_member_id($otherMemberId);
        if (!$resolvedOtherId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $conversation = $this->findConversation($currentMemberId, $resolvedOtherId);
        if ($conversation) {
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_id', $resolvedOtherId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        $this->invalidateChatCaches([$currentMemberId, $resolvedOtherId]);

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $currentMemberId = $this->currentMemberId();
        if (!$currentMemberId) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $count = Cache::remember($this->unreadCacheKey((string) $currentMemberId), now()->addSeconds(5), function () use ($currentMemberId) {
            $conversationIds = ChatParticipant::query()
                ->where('member_id', $currentMemberId)
                ->pluck('conversation_id')
                ->all();

            if (empty($conversationIds)) {
                return 0;
            }

            return ChatMessage::query()
                ->whereIn('conversation_id', $conversationIds)
                ->where('sender_id', '!=', $currentMemberId)
                ->where('is_read', false)
                ->count();
        });

        return response()->json(['success' => true, 'unread' => $count]);
    }

    private function findConversation(int $memberId, int $otherMemberId): ?ChatConversation
    {
        $conversationId = ChatParticipant::query()
            ->select('conversation_id')
            ->whereIn('member_id', [$memberId, $otherMemberId])
            ->groupBy('conversation_id')
            ->havingRaw('COUNT(DISTINCT member_id) = 2')
            ->value('conversation_id');

        return $conversationId ? ChatConversation::find($conversationId) : null;
    }

    private function getOrCreateConversation(int $memberId, int $otherMemberId): ChatConversation
    {
        $conversation = $this->findConversation($memberId, $otherMemberId);
        if ($conversation) {
            return $conversation;
        }

        $conversation = ChatConversation::create([
            'conversation_type' => 'individual',
            'created_by' => Auth::id() ?? 1,
        ]);

        ChatParticipant::insert([
            [
                'conversation_id' => $conversation->id,
                'member_id' => $memberId,
                'role' => 'member',
                'joined_at' => now(),
            ],
            [
                'conversation_id' => $conversation->id,
                'member_id' => $otherMemberId,
                'role' => 'member',
                'joined_at' => now(),
            ],
        ]);

        return $conversation;
    }
}
