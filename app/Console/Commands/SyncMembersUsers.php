<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\User;
use App\Services\UserMemberSyncService;

class SyncMembersUsers extends Command
{
    protected $signature = 'sync:members-users';
    protected $description = 'Sync members and users tables';

    public function handle()
    {
        $this->info('Syncing members and users...');

        $report = app(UserMemberSyncService::class)->reconcileMissingLinks();

        $this->info('Sync completed!');
        $this->info('Total Members: ' . Member::count());
        $this->info('Total Users: ' . User::count());
        $this->line('Users checked: ' . $report['users_checked']);
        $this->line('Members checked: ' . $report['members_checked']);
        $this->line('Members created: ' . $report['members_created']);
        $this->line('Members updated: ' . $report['members_updated']);
        $this->line('Members restored: ' . $report['members_restored']);
        $this->line('Users created: ' . $report['users_created']);
        $this->line('Users updated: ' . $report['users_updated']);
        $this->line('Members linked: ' . $report['members_linked']);
    }
}
