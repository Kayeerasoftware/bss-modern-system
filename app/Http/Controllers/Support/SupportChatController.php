<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Services\Support\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportChatController extends Controller
{
    public function respond(Request $request, SupportChatService $supportChatService): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:50',
            'message' => 'required|string|max:2000',
            'path' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }

        $result = $supportChatService->respond(
            $user,
            (string) ($validated['category'] ?? 'general'),
            (string) ($validated['message'] ?? ''),
            (string) ($validated['path'] ?? '')
        );

        return response()->json([
            'success' => true,
            'data' => [
                'reply' => $result['reply'],
                'quick_actions' => $result['quick_actions'],
                'suggested_category' => $result['suggested_category'],
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }
}

