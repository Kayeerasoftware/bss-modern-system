Route::get('/debug-members', function() {
    $currentUser = auth()->user();
    $currentMember = $currentUser ? $currentUser->member : null;
    $currentMemberId = $currentMember ? $currentMember->member_id : null;
    
    $members = \App\Models\Member::limit(3)->get();
    
    if ($currentMemberId) {
        foreach ($members as $member) {
            if ($member->member_id !== $currentMemberId) {
                $member->unread_count = \App\Models\ChatMessage::where('sender_id', $member->member_id)
                    ->where('receiver_id', $currentMemberId)
                    ->where('is_read', false)
                    ->count();
                
                $member->last_message = \App\Models\ChatMessage::where(function($q) use ($currentMemberId, $member) {
                    $q->where(function($q2) use ($currentMemberId, $member) {
                        $q2->where('sender_id', $currentMemberId)->where('receiver_id', $member->member_id);
                    })->orWhere(function($q2) use ($currentMemberId, $member) {
                        $q2->where('sender_id', $member->member_id)->where('receiver_id', $currentMemberId);
                    });
                })->latest()->first();
            }
        }
    }
    
    return response()->json([
        'current_user_id' => $currentUser ? $currentUser->id : null,
        'current_member_id' => $currentMemberId,
        'members' => $members->map(function($m) {
            return [
                'id' => $m->id,
                'member_id' => $m->member_id,
                'name' => $m->full_name,
                'unread_count' => $m->unread_count ?? 0,
                'last_message' => $m->last_message ? [
                    'message' => $m->last_message->message,
                    'sender_id' => $m->last_message->sender_id,
                    'is_read' => $m->last_message->is_read,
                    'created_at' => $m->last_message->created_at
                ] : null
            ];
        })
    ]);
})->middleware('auth');
