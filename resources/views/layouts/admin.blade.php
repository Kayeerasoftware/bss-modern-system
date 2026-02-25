<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - BSS Investment Group</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/components/nav.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/roles/admin/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dark-mode.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="{{ asset('assets/js/main2.js') }}"></script>
    <script src="{{ asset('js/chat.js') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes slide-right {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        @keyframes slide-text {
            0% { left: 0%; }
            100% { left: 100%; }
        }
        .animate-slide-right {
            animation: slide-right 5s ease-in-out forwards;
        }
        .animate-slide-text {
            animation: slide-text 5s ease-in-out forwards;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
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
                'contact' => $user->member->contact ?? null,
                'member_id' => $user->member->member_id ?? null,
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
            ];
        });
@endphp
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300" x-data="{ 
    ...window.adminPanel(),
    ...window.chatModule(),
    currentUserId: {{ auth()->id() }},
    members: {{ Js::from($usersData) }},
    originalMembers: {{ Js::from($usersData) }}
}" x-init="initChat(); filteredMembersChat = originalMembers;">
    @include('components.loading-screen')
    @include('partials.navs.admin-topnav')
    @include('partials.navs.admin-sidenav')

    <div class="main-content transition-all duration-300"
         :class="{
             'ml-0': !sidebarOpen,
             'ml-36': sidebarOpen,
             'lg:ml-16': sidebarCollapsed,
             'lg:ml-36': !sidebarCollapsed
         }">
        <div class="max-w-7xl mx-auto px-4 py-6">
            @yield('content')
        </div>
    </div>

    @stack('modals')
    @include('partials.admin.modals.member-chat')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/admin/charts.js') }}"></script>
    @stack('scripts')
</body>
</html>
