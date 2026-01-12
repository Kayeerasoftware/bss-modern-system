@echo off
echo === BSS Chat System Setup ===
echo.

echo Running migration...
php artisan migrate --path=database/migrations/2024_01_20_000004_create_chat_messages_table.php

echo.
echo === Setup Complete! ===
echo.
echo Chat Features:
echo - Real-time messaging between members
echo - Message persistence in database
echo - Unread message counts
echo - Conversation history
echo - Typing indicators
echo - WhatsApp-like interface
echo.
echo API Endpoints:
echo - POST /api/chat/send
echo - GET /api/chat/messages/{senderId}/{receiverId}
echo - GET /api/chat/conversations/{memberId}
echo - POST /api/chat/mark-read
echo.
pause
