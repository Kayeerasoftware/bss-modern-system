<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier Dashboard') - BSS Investment Group</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/components/nav.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/roles/admin/admin.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    @include('partials.alpine-init')
</head>
@php
    $usersData = \App\Models\User::with('member')
        ->where('id', '!=', auth()->id())
        ->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->member->contact ?? null,
                'member_id' => $user->member->member_id ?? null,
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
            ];
        });
@endphp
<body class="bg-gray-50" x-data="(() => {
    if (typeof window.chatModule !== 'function') {
        console.error('chatModule not loaded!');
        return { sidebarOpen: false, sidebarCollapsed: false };
    }
    return {
        ...window.chatModule(),
        sidebarOpen: false,
        sidebarCollapsed: false,
        showProfileDropdown: false,
        showLogoModal: false,
        showShareholderModal: false,
        showChatModal: false,
        showMemberChatModal: false,
        notificationStats: { unread: 0 },
        adminProfile: { name: '{{ auth()->user()->name ?? "Cashier" }}', role: 'Cashier' },
        members: {{ Js::from($usersData) }},
        originalMembers: {{ Js::from($usersData) }},
        profilePicture: '{{ auth()->user()->profile_picture ? asset("storage/" . auth()->user()->profile_picture) : "" }}',
        async fetchNotificationCount() {
            try {
                const response = await fetch('{{ route("cashier.notifications.unread-count") }}');
                const data = await response.json();
                this.notificationStats.unread = data.count;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        }
    };
})()" x-init="if (typeof initChat === 'function') { initChat(); filteredMembersChat = originalMembers; fetchNotificationCount(); setInterval(() => fetchNotificationCount(), 30000); }">
    @include('partials.navs.cashier-topnav')
    @include('partials.navs.cashier-sidenav')

    <div class="main-content transition-all duration-300" :class="sidebarCollapsed ? 'ml-0 lg:ml-20' : 'ml-0 lg:ml-36'" style="margin-top: 3rem;">
        <div class="max-w-full">
            @yield('content')
        </div>
    </div>
    
    @include('partials.admin.modals.member-chat')
    
    @stack('scripts')
</body>
</html>
