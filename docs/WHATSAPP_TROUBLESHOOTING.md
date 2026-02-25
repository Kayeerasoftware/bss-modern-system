# WhatsApp Member List - Troubleshooting Guide

## Quick Test Steps

### 1. Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### 2. Verify Data Exists
```bash
php artisan tinker --execute="echo 'Members: ' . App\Models\Member::count() . PHP_EOL; echo 'Messages: ' . App\Models\ChatMessage::count() . PHP_EOL;"
```

### 3. Test Debug Endpoint
Login as shareholder, then visit:
```
http://localhost:8000/debug-members
```

Expected JSON output should show:
- current_member_id
- members array with unread_count and last_message

### 4. Check Shareholder Route
Visit the actual members page:
```
http://localhost:8000/shareholder/members
```

## Common Issues & Fixes

### Issue 1: No unread badges showing
**Cause**: Current user doesn't have a member record
**Fix**: Ensure logged-in user has associated member in `members` table

### Issue 2: "No messages yet" for all members
**Cause**: No chat messages in database
**Fix**: Run seeder
```bash
php artisan db:seed --class=ChatMessageSeeder
```

### Issue 3: Read receipts not showing
**Cause**: Sender ID doesn't match current user's member_id
**Fix**: Verify auth()->user()->member->member_id exists

### Issue 4: Timestamps not displaying
**Cause**: created_at not cast to datetime in ChatMessage model
**Fix**: Already fixed in ChatMessage model (casts array)

## Manual Data Insert (if seeder fails)

```php
php artisan tinker

// Get two members
$member1 = App\Models\Member::first();
$member2 = App\Models\Member::skip(1)->first();

// Create test messages
App\Models\ChatMessage::create([
    'sender_id' => $member1->member_id,
    'receiver_id' => $member2->member_id,
    'message' => 'Hello! How are you?',
    'is_read' => false,
    'created_at' => now()->subHours(2)
]);

App\Models\ChatMessage::create([
    'sender_id' => $member2->member_id,
    'receiver_id' => $member1->member_id,
    'message' => 'I am good, thanks!',
    'is_read' => true,
    'created_at' => now()->subHours(1)
]);

echo "Messages created successfully!";
```

## Verify Feature is Working

### Expected Visual Elements:
1. ✅ Green badge on avatar (when unread > 0)
2. ✅ Badge shows number or "9+"
3. ✅ Last message preview (truncated to 40 chars)
4. ✅ Timestamp in human format ("2 hours ago")
5. ✅ Read receipts (✓ or ✓✓)

### Test Checklist:
- [ ] Login as shareholder user
- [ ] Navigate to Members page
- [ ] See unread count badges
- [ ] See last message previews
- [ ] See timestamps
- [ ] See read receipts on sent messages
- [ ] Click member row to view details

## Still Not Working?

### Check Browser Console
Press F12 and look for JavaScript errors

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Enable Debug Mode
In `.env`:
```
APP_DEBUG=true
```

### Verify Relationships
```bash
php artisan tinker --execute="$m = App\Models\Member::first(); echo 'Sent: ' . $m->sentMessages()->count() . PHP_EOL; echo 'Received: ' . $m->receivedMessages()->count() . PHP_EOL;"
```

## Contact Support
If issue persists, provide:
1. Laravel version
2. PHP version
3. Error messages from logs
4. Screenshot of members page
5. Output from debug endpoint
