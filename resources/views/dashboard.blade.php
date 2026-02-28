<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BSS-2025 Investment Group - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body data-user-role="{{ Auth::user()->role }}" data-user-roles='@json(Auth::user()->roles_list ?? [])'>
    <!-- Header -->
    <div class="header-banner">
        <div class="header-content">
            <div class="flex items-center justify-center gap-4">
                <img src="{{ asset('assets/images/logo.png') }}" alt="BSS Logo" class="w-16 h-16 border-4 border-white shadow-lg object-cover">
                <div>
                    <h1 class="header-title">BSS-2025 Investment Group</h1>
                    <p class="header-subtitle">Empowering Bunya Secondary School Alumni</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-500 py-1.5 lg:py-3">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-1.5 lg:gap-4">
                <div class="flex items-center gap-2 lg:gap-4">
                    <h1 class="text-white text-xl sm:text-2xl lg:text-5xl font-bold">Welcome, <span class="text-gray-900">{{ Auth::user()->name }}</span>!</h1>
                    <img src="{{ Auth::user()->profile_picture_url }}" alt="User" class="w-10 h-10 lg:w-16 lg:h-16 rounded-xl border-2 border-white object-cover" style="box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                </div>
                <div class="w-full lg:w-auto">
                    @if(Auth::user()->role === 'client')
                        <div>
                            <h2 class="text-white text-xs lg:text-sm font-bold lg:text-right mb-1"><i class="fas fa-user-circle mr-1"></i>Client Dashboard</h2>
                            <div class="flex gap-1 lg:gap-2">
                                @php
                                    $member = \App\Models\Member::where('user_id', Auth::id())->first();
                                    $savings = $member?->savings ?? 0;
                                    $loan = $member?->loan ?? 0;
                                    $netBalance = $savings - $loan;
                                @endphp
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-piggy-bank mr-0.5 lg:mr-1"></i>Savings</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($savings, 0) }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-hand-holding-usd mr-0.5 lg:mr-1"></i>Loan</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($loan, 0) }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-wallet mr-0.5 lg:mr-1"></i>Balance</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($netBalance, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif(Auth::user()->role === 'shareholder')
                        <div>
                            <h2 class="text-white text-xs lg:text-sm font-bold lg:text-right mb-1"><i class="fas fa-chart-line mr-1"></i>Shareholder Dashboard</h2>
                            <div class="flex gap-1 lg:gap-2">
                                @php
                                    $member = \App\Models\Member::where('user_id', Auth::id())->first();
                                    $savings = $member?->savings ?? 0;
                                    $loan = $member?->loan ?? 0;
                                    $netBalance = $savings - $loan;
                                @endphp
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-piggy-bank mr-0.5 lg:mr-1"></i>Total Savings</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($savings, 0) }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-hand-holding-usd mr-0.5 lg:mr-1"></i>Total Loan</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($loan, 0) }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-balance-scale mr-0.5 lg:mr-1"></i>Net Balance</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($netBalance, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif(Auth::user()->role === 'cashier')
                        <div>
                            <h2 class="text-white text-xs lg:text-sm font-bold lg:text-right mb-1"><i class="fas fa-cash-register mr-1"></i>Cashier Dashboard</h2>
                            <div class="flex gap-1 lg:gap-2">
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-piggy-bank mr-0.5 lg:mr-1"></i>Total Savings</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($stats['totalSavings'] ?? 0, 0) }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-hand-holding-usd mr-0.5 lg:mr-1"></i>Active Loans</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ $stats['totalLoans'] ?? 0 }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-wallet mr-0.5 lg:mr-1"></i>Available Funds</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ number_format($stats['availableFunds'] ?? 0, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif(Auth::user()->role === 'td')
                        <div>
                            <h2 class="text-white text-xs lg:text-sm font-bold lg:text-right mb-1"><i class="fas fa-project-diagram mr-1"></i>Technical Director</h2>
                            <div class="flex gap-1 lg:gap-2">
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-tasks mr-0.5 lg:mr-1"></i>Active Projects</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ $stats['activeProjects'] ?? 0 }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-users-cog mr-0.5 lg:mr-1"></i>Team Members</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ $stats['totalMembers'] ?? 0 }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-1.5 lg:px-2 py-0.5 lg:py-1 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold"><i class="fas fa-check-circle mr-0.5 lg:mr-1"></i>Completed</p>
                                    <p class="text-white text-[10px] lg:text-xs">{{ $stats['nearingCompletion'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif(Auth::user()->role === 'ceo')
                        <div>
                            <h2 class="text-white text-xs lg:text-base font-bold lg:text-center mb-1 lg:mb-2"><i class="fas fa-crown mr-1"></i>CEO & Chairperson</h2>
                            <div class="flex gap-1.5 lg:gap-3">
                                <div class="bg-white bg-opacity-20 rounded px-2 lg:px-4 py-1 lg:py-2 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold mb-0.5 lg:mb-1"><i class="fas fa-users mr-0.5 lg:mr-1"></i>Total Members</p>
                                    <p class="text-white text-xs lg:text-sm font-bold">{{ $stats['totalMembers'] ?? 0 }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-2 lg:px-4 py-1 lg:py-2 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold mb-0.5 lg:mb-1"><i class="fas fa-chart-line mr-0.5 lg:mr-1"></i>Member Growth</p>
                                    <p class="text-white text-xs lg:text-sm font-bold">{{ $stats['memberGrowth'] ?? '0.0' }}%</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-2 lg:px-4 py-1 lg:py-2 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold mb-0.5 lg:mb-1"><i class="fas fa-project-diagram mr-0.5 lg:mr-1"></i>Active Projects</p>
                                    <p class="text-white text-xs lg:text-sm font-bold">{{ $stats['activeProjects'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif(Auth::user()->role === 'admin')
                        <div>
                            <h2 class="text-white text-xs lg:text-base font-bold lg:text-center mb-1 lg:mb-2"><i class="fas fa-user-shield mr-1"></i>Admin Dashboard</h2>
                            <div class="flex gap-1.5 lg:gap-3">
                                <div class="bg-white bg-opacity-20 rounded px-2 lg:px-4 py-1 lg:py-2 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold mb-0.5 lg:mb-1"><i class="fas fa-users mr-0.5 lg:mr-1"></i>Total Users</p>
                                    <p class="text-white text-xs lg:text-sm font-bold">{{ $stats['totalMembers'] ?? 0 }}</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-2 lg:px-4 py-1 lg:py-2 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold mb-0.5 lg:mb-1"><i class="fas fa-cogs mr-0.5 lg:mr-1"></i>System Status</p>
                                    <p class="text-white text-xs lg:text-sm font-bold">Active</p>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded px-2 lg:px-4 py-1 lg:py-2 text-center">
                                    <p class="text-white text-[10px] lg:text-xs font-semibold mb-0.5 lg:mb-1"><i class="fas fa-shield-alt mr-0.5 lg:mr-1"></i>Security</p>
                                    <p class="text-white text-xs lg:text-sm font-bold">OK</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="lg:text-right">
                            <h2 class="text-white text-xs lg:text-sm font-bold"><i class="fas fa-tachometer-alt mr-1"></i>Main Dashboard</h2>
                            <p class="text-blue-100 text-[10px] lg:text-xs">BSS Investment Group Portal</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <!-- Role Switch Loading Overlay -->
    <div id="role-loading-overlay" class="role-loading-overlay hidden" aria-live="polite" aria-busy="true">
        <div class="role-loading-aurora" aria-hidden="true"></div>
        <div class="role-loading-card">
            <div class="role-loader-stage" aria-hidden="true">
                <div class="role-loader-orbit role-loader-orbit-one"></div>
                <div class="role-loader-orbit role-loader-orbit-two"></div>
                <div class="role-loader-core">
                    <i id="role-loading-icon" class="fas fa-user-shield"></i>
                </div>
            </div>
            <h3 class="role-loading-title">Switching Workspace</h3>
            <p id="role-loading-text" class="role-loading-text">Role selected. Initializing your dashboard...</p>
            <p id="role-loading-subtext" class="role-loading-subtext">Syncing permissions, widgets, and latest insights</p>
            <div class="role-loading-progress" aria-hidden="true">
                <span class="role-loading-progress-bar"></span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-gray-900 text-white p-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base sm:text-lg lg:text-2xl font-semibold">
                    <span class="hidden lg:inline">Select your active role to enter the system (Your current active role: <span class="text-green-400">{{ ucfirst(Auth::user()->role) }} <i class="fas fa-check"></i></span>)</span>
                    <span class="lg:hidden">Select your active role</span>
                    <span class="block lg:hidden text-sm text-gray-300 mt-1">Current: <span class="text-green-400 font-semibold">{{ ucfirst(Auth::user()->role) }} <i class="fas fa-check"></i></span></span>
                </h3>
                <span class="text-green-400 font-semibold flex items-center gap-2 text-sm">
                    <img src="{{ Auth::user()->profile_picture_url }}" alt="User" class="w-8 h-8 rounded-full object-cover">
                    <span class="hidden lg:inline">{{ Auth::user()->name }} (<span class="text-white">{{ ucfirst(Auth::user()->role) }}</span>)</span>
                </span>
            </div>
            <div class="flex flex-wrap justify-between items-center">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:flex lg:flex-wrap gap-2 lg:gap-3 w-full lg:w-auto">
                    @php
                        $defaultRole = strtolower((string) (Auth::user()->role ?? ''));
                        $userRoles = array_values(array_unique(array_map(
                            fn ($role) => strtolower((string) $role),
                            Auth::user()->roles_list ?? []
                        )));
                        if ($defaultRole !== '' && !in_array($defaultRole, $userRoles, true)) {
                            $userRoles[] = $defaultRole;
                        }

                        $roleStatusText = function (string $roleKey) use ($userRoles, $defaultRole): string {
                            if ($roleKey === $defaultRole) {
                                return 'Default_Active';
                            }

                            return in_array($roleKey, $userRoles, true) ? 'Active' : 'Inactive';
                        };

                        $roleStatusClass = function (string $roleKey) use ($userRoles, $defaultRole): string {
                            if ($roleKey === $defaultRole || in_array($roleKey, $userRoles, true)) {
                                return 'text-green-400';
                            }

                            return 'text-red-400';
                        };
                    @endphp
                    <div class="text-center">
                        <button onclick="selectRole('client')" class="navbar-item w-full lg:w-auto {{ in_array('client', $userRoles) ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' : 'bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800' }} px-3 lg:px-5 py-2 lg:py-3 font-semibold text-sm">
                            <i class="fas fa-user-circle mr-0 lg:mr-2 text-base lg:text-lg block lg:inline mb-1 lg:mb-0"></i><span class="text-xs lg:text-base">Client</span>
                        </button>
                        <p class="role-status-text text-xs mt-1 {{ $roleStatusClass('client') }}">{{ $roleStatusText('client') }}</p>
                    </div>
                    <div class="text-center">
                        <button onclick="selectRole('shareholder')" class="navbar-item w-full lg:w-auto {{ in_array('shareholder', $userRoles) ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' : 'bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800' }} px-3 lg:px-5 py-2 lg:py-3 font-semibold text-sm">
                            <i class="fas fa-chart-line mr-0 lg:mr-2 text-base lg:text-lg block lg:inline mb-1 lg:mb-0"></i><span class="text-xs lg:text-base">Shareholder</span>
                        </button>
                        <p class="role-status-text text-xs mt-1 {{ $roleStatusClass('shareholder') }}">{{ $roleStatusText('shareholder') }}</p>
                    </div>
                    <div class="text-center">
                        <button onclick="selectRole('cashier')" class="navbar-item w-full lg:w-auto {{ in_array('cashier', $userRoles) ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' : 'bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800' }} px-3 lg:px-5 py-2 lg:py-3 font-semibold text-sm">
                            <i class="fas fa-cash-register mr-0 lg:mr-2 text-base lg:text-lg block lg:inline mb-1 lg:mb-0"></i><span class="text-xs lg:text-base">Cashier</span>
                        </button>
                        <p class="role-status-text text-xs mt-1 {{ $roleStatusClass('cashier') }}">{{ $roleStatusText('cashier') }}</p>
                    </div>
                    <div class="text-center">
                        <button onclick="selectRole('td')" class="navbar-item w-full lg:w-auto {{ in_array('td', $userRoles) ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' : 'bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800' }} px-3 lg:px-5 py-2 lg:py-3 font-semibold text-sm">
                            <i class="fas fa-project-diagram mr-0 lg:mr-2 text-base lg:text-lg block lg:inline mb-1 lg:mb-0"></i><span class="text-xs lg:text-base">Technical Director</span>
                        </button>
                        <p class="role-status-text text-xs mt-1 {{ $roleStatusClass('td') }}">{{ $roleStatusText('td') }}</p>
                    </div>
                    <div class="text-center">
                        <button onclick="selectRole('ceo')" class="navbar-item w-full lg:w-auto {{ in_array('ceo', $userRoles) ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' : 'bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800' }} px-3 lg:px-5 py-2 lg:py-3 font-semibold text-sm">
                            <i class="fas fa-crown mr-0 lg:mr-2 text-base lg:text-lg block lg:inline mb-1 lg:mb-0"></i><span class="text-xs lg:text-base">CEO & Chairperson</span>
                        </button>
                        <p class="role-status-text text-xs mt-1 {{ $roleStatusClass('ceo') }}">{{ $roleStatusText('ceo') }}</p>
                    </div>
                    <div class="text-center">
                        <button onclick="selectRole('admin')" class="navbar-item w-full lg:w-auto {{ in_array('admin', $userRoles) ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' : 'bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800' }} px-3 lg:px-5 py-2 lg:py-3 font-semibold text-sm">
                            <i class="fas fa-user-shield mr-0 lg:mr-2 text-base lg:text-lg block lg:inline mb-1 lg:mb-0"></i><span class="text-xs lg:text-base">Admin</span>
                        </button>
                        <p class="role-status-text text-xs mt-1 {{ $roleStatusClass('admin') }}">{{ $roleStatusText('admin') }}</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-2 lg:mt-0 w-full lg:w-auto justify-end">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-orange-600 px-3 lg:px-4 py-2 rounded-lg hover:bg-orange-700 transition text-sm">
                            <i class="fas fa-sign-out-alt mr-1 lg:mr-2"></i><span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>



    <!-- Empty Boxes Section -->
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
            <div id="roleCard" class="role-card rounded-xl shadow-lg p-4 h-64 flex flex-col" style="--role-start: #6366f1; --role-end: #8b5cf6;">
                <div id="roleDefault" class="flex-1 flex flex-col items-center justify-center">
                    <i class="fas fa-user-check text-6xl text-indigo-400 mb-3"></i>
                    <h3 class="text-base font-bold text-gray-700 text-center">Select any Role from the above roles</h3>
                    <p class="text-xs text-gray-500 mt-1">View detailed description</p>
                </div>
                <div id="roleSelected" class="hidden flex-1 flex flex-col">
                    <div class="mb-3">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-3">
                                <div id="roleIcon" class="role-icon"></div>
                                <h3 id="roleTitle" class="text-xl font-bold text-gray-800"></h3>
                            </div>
                            <div id="roleStatus"></div>
                        </div>
                    </div>
                    <div id="roleFeatures" class="feature-list flex-1 space-y-1 mb-3"></div>
                    <button id="roleButton" onclick="goToRole()" class="go-btn w-full hidden">
                        Launch Dashboard <i class="fas fa-rocket ml-2"></i>
                    </button>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden h-64">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-3 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                        <h3 class="text-sm font-bold text-white">Member Progress</h3>
                    </div>
                    <span class="text-xs text-blue-100">{{ date('F d, Y') }}</span>
                </div>
                <div class="px-3 pb-3 pt-2" style="height: calc(100% - 40px);">
                    <canvas id="memberChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden h-64">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-3 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-project-diagram text-white text-lg"></i>
                        <h3 class="text-sm font-bold text-white">Current Projects</h3>
                    </div>
                    <i class="fas fa-images text-purple-100 text-xs"></i>
                </div>
                @php
                    $projectPhotos = \App\Models\DashboardPhoto::active()->projects()->orderBy('order')->take(20)->get();
                @endphp
                <div id="projectContainer" class="relative" style="height: calc(100% - 40px);">
                    <div id="projectGrid" class="grid grid-cols-2 gap-1 h-full p-2">
                        @foreach($projectPhotos->take(4) as $photo)
                            <div class="relative w-full h-full">
                                <img src="{{ $photo->photo_path }}" alt="{{ $photo->title }}" class="w-full h-full object-cover rounded" title="{{ $photo->title }}">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-1">
                                    <p class="text-white text-xs font-semibold">{{ $photo->title }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @foreach($projectPhotos as $index => $photo)
                        <div class="project-slide absolute w-full h-full rounded opacity-0" data-index="{{ $index }}">
                            <img src="{{ $photo->photo_path }}" alt="{{ $photo->title }}" class="w-full h-full object-cover rounded">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                <p class="text-white text-xs font-semibold">{{ $photo->title }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden h-64">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-3 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-handshake text-white text-lg"></i>
                        <h3 class="text-sm font-bold text-white">Meetings & Conferences</h3>
                    </div>
                    <i class="fas fa-calendar-alt text-green-100 text-xs"></i>
                </div>
                @php
                    $meetingPhotos = \App\Models\DashboardPhoto::active()->meetings()->orderBy('order')->take(20)->get();
                @endphp
                <div id="meetingContainer" class="relative" style="height: calc(100% - 40px);">
                    <div id="meetingGrid" class="grid grid-cols-2 gap-1 h-full p-2">
                        @foreach($meetingPhotos->take(4) as $photo)
                            <div class="relative w-full h-full">
                                <img src="{{ $photo->photo_path }}" alt="{{ $photo->title }}" class="w-full h-full object-cover rounded" title="{{ $photo->title }}">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-1">
                                    <p class="text-white text-xs font-semibold">{{ $photo->title }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @foreach($meetingPhotos as $index => $photo)
                        <div class="meeting-slide absolute w-full h-full rounded opacity-0" data-index="{{ $index }}">
                            <img src="{{ $photo->photo_path }}" alt="{{ $photo->title }}" class="w-full h-full object-cover rounded">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                <p class="text-white text-xs font-semibold">{{ $photo->title }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script id="chartData" type="application/json">
        @php
            $roleData = [
                'client' => \App\Models\User::where('role', 'client')->count(),
                'shareholder' => \App\Models\User::where('role', 'shareholder')->count(),
                'cashier' => \App\Models\User::where('role', 'cashier')->count(),
                'td' => \App\Models\User::where('role', 'td')->count(),
                'ceo' => \App\Models\User::where('role', 'ceo')->count(),
                'admin' => \App\Models\User::where('role', 'admin')->count(),
                'total' => \App\Models\User::count(),
                'active' => \App\Models\User::count()
            ];
        @endphp
        {!! json_encode($roleData) !!}
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-3">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">© {{ date('Y') }} BSS-2025 Investment Group. All rights reserved. | Empowering Bunya Secondary School Alumni</p>
        </div>
    </footer>
</body>
</html>
