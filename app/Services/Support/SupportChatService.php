<?php

namespace App\Services\Support;

use App\Models\User;
use Illuminate\Support\Str;

class SupportChatService
{
    /**
     * @return array{reply:string,quick_actions:array<int,string>,suggested_category:string}
     */
    public function respond(User $user, string $category, string $message, string $path = ''): array
    {
        $normalizedCategory = $this->normalizeCategory($category);
        $normalizedMessage = Str::lower(trim($message));

        if ($normalizedMessage === '') {
            return [
                'reply' => 'Please describe your issue in one short sentence so I can help quickly.',
                'quick_actions' => ['Settings Help', 'User Issue', 'Error', 'Performance', 'Data Sync'],
                'suggested_category' => $normalizedCategory,
            ];
        }

        $inferredCategory = $this->inferCategory($normalizedMessage, $normalizedCategory);
        $role = Str::lower((string) ($user->role ?? 'member'));

        $reply = $this->buildReply($inferredCategory, $normalizedMessage, $role, $path);
        $quickActions = $this->quickActionsForCategory($inferredCategory);

        return [
            'reply' => $reply,
            'quick_actions' => $quickActions,
            'suggested_category' => $inferredCategory,
        ];
    }

    private function normalizeCategory(string $category): string
    {
        $value = Str::lower(trim($category));

        return match ($value) {
            'settings', 'settings help', 'settings_help' => 'settings_help',
            'user issue', 'user_issue', 'issue' => 'user_issue',
            'error', 'bug' => 'error',
            'performance', 'slow', 'latency' => 'performance',
            'account', 'account access', 'access' => 'account_access',
            'data', 'data sync', 'sync' => 'data_sync',
            'billing', 'payments', 'payment' => 'billing',
            default => 'general',
        };
    }

    private function inferCategory(string $message, string $fallback): string
    {
        $patterns = [
            'error' => ['error', 'exception', '500', '404', 'failed', 'crash', 'broken'],
            'performance' => ['slow', 'lag', 'delay', 'freeze', 'timeout', 'loading long'],
            'account_access' => ['login', 'password', 'access denied', 'permission', 'locked'],
            'data_sync' => ['not sync', 'mismatch', 'missing records', 'different count', 'not updated'],
            'settings_help' => ['setting', 'configure', 'preference', 'profile setting'],
            'billing' => ['payment', 'invoice', 'charge', 'billing'],
            'user_issue' => ['user', 'member', 'role', 'dashboard', 'cannot see'],
        ];

        foreach ($patterns as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($message, $keyword)) {
                    return $category;
                }
            }
        }

        return $fallback;
    }

    private function buildReply(string $category, string $message, string $role, string $path): string
    {
        $context = $path !== '' ? " Current page: {$path}." : '';

        return match ($category) {
            'settings_help' =>
                "Open Settings and save one change, then refresh once to confirm it persisted. If it does not persist, clear browser cache and retry.{$context}",
            'user_issue' =>
                "Check the affected user's role assignment and active status first. If counts look mismatched between pages, refresh the listing and verify filters (status, role, date) are not excluding records.{$context}",
            'error' =>
                "Capture the exact error text, page URL, and time it happened. Then retry once. If it repeats, report those details so logs can be traced quickly.{$context}",
            'performance' =>
                "This looks like a speed issue. Test once with fewer filters and smaller page size, then retry. If still slow, note the exact page and action so query/performance tuning can be targeted.{$context}",
            'account_access' =>
                "Verify your account is active and the role has permission for this page. If login/redirect loops continue, sign out and sign in again to refresh session permissions.{$context}",
            'data_sync' =>
                "For sync mismatch, compare filters and soft-deleted records first. Then refresh and check if totals align across dashboard cards and table pages.{$context}",
            'billing' =>
                "For payment/billing issues, confirm transaction status and reference ID. If status is pending too long, retry after refresh and avoid duplicate submissions.{$context}",
            default =>
                "I can help with Settings Help, User Issue, Error, Performance, Account Access, Data Sync, and Billing. Share the exact page and what you expected vs what happened.{$context}",
        };
    }

    /**
     * @return array<int,string>
     */
    private function quickActionsForCategory(string $category): array
    {
        return match ($category) {
            'settings_help' => ['Settings Help', 'Account Access', 'User Issue'],
            'user_issue' => ['User Issue', 'Data Sync', 'Error'],
            'error' => ['Error', 'Performance', 'Data Sync'],
            'performance' => ['Performance', 'Error', 'Data Sync'],
            'account_access' => ['Account Access', 'Settings Help', 'Error'],
            'data_sync' => ['Data Sync', 'User Issue', 'Error'],
            'billing' => ['Billing', 'Error', 'User Issue'],
            default => ['Settings Help', 'User Issue', 'Error'],
        };
    }
}

