@extends('layouts.td')

@section('title', 'TD Dashboard')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-3 md:p-4">
        <div class="flex items-center justify-between gap-2">
            <div class="flex-1 min-w-0">
                <h1 class="text-sm sm:text-lg md:text-2xl font-bold text-white truncate">Welcome, <span class="text-black text-base sm:text-xl md:text-3xl">{{ auth()->user()->name }}</span> <span class="text-purple-200 font-normal text-xs sm:text-sm md:text-lg">(Technical Director)</span> 👋</h1>
                <p class="text-purple-100 text-xs sm:text-sm mt-0.5 md:mt-1">Project & Technical Oversight Dashboard</p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 sm:px-3 sm:py-1.5 md:px-4 md:py-2 text-right flex-shrink-0">
                <p class="text-white text-xs sm:text-sm font-semibold whitespace-nowrap">{{ now()->format('l, F d, Y') }}</p>
                <p class="text-purple-100 text-xs mt-0.5 whitespace-nowrap">{{ now()->format('h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-indigo-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Dashboard data...</span>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-3">
        <!-- Total Projects -->
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-indigo-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-project-diagram text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Projects</p>
                    <h3 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $stats['totalProjects'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-blue-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-tasks text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Active</p>
                    <h3 class="text-xl md:text-3xl font-bold text-blue-600 leading-tight">{{ $stats['activeProjects'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-green-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-check-circle text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Completed</p>
                    <h3 class="text-xl md:text-3xl font-bold text-green-600 leading-tight">{{ $stats['completedProjects'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Pending Projects -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-orange-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-clock text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Pending</p>
                    <h3 class="text-xl md:text-3xl font-bold text-orange-600 leading-tight">{{ $stats['pendingProjects'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Members -->
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-purple-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-users text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Team Members</p>
                    <h3 class="text-xl md:text-3xl font-bold text-purple-600 leading-tight">{{ $stats['totalMembers'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-teal-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-user-shield text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Active Users</p>
                    <h3 class="text-xl md:text-3xl font-bold text-teal-600 leading-tight">{{ $stats['activeUsers'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Loans -->
        <div class="bg-gradient-to-r from-pink-50 to-pink-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-pink-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-money-bill-wave text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Total Loans</p>
                    <h3 class="text-xl md:text-3xl font-bold text-pink-600 leading-tight">{{ $stats['totalLoans'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Today's Transactions -->
        <div class="bg-gradient-to-r from-cyan-50 to-cyan-100 rounded-lg shadow-md p-2 md:p-3 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-cyan-200">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 p-2 md:p-2.5 rounded-lg shadow">
                    <i class="fas fa-exchange-alt text-white text-base md:text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs md:text-sm text-gray-600 font-semibold leading-tight">Today's Trans.</p>
                    <h3 class="text-xl md:text-3xl font-bold text-cyan-600 leading-tight">{{ $stats['todayTransactions'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Projects -->
        <div class="bg-white rounded-xl shadow-xl p-6 hover:shadow-2xl transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-project-diagram text-indigo-600 mr-2"></i>Recent Projects
                </h3>
                <a href="{{ route('td.projects.index') }}" class="text-indigo-600 text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentProjects as $project)
                <div class="flex items-center justify-between p-3 hover:bg-indigo-50 rounded-lg transition cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-project-diagram text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $project->name }}</p>
                            <p class="text-xs text-gray-500">{{ $project->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full font-semibold 
                        {{ $project->status == 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $project->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $project->status == 'pending' ? 'bg-orange-100 text-orange-800' : '' }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-8">No recent projects</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Members -->
        <div class="bg-white rounded-xl shadow-xl p-6 hover:shadow-2xl transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-users text-purple-600 mr-2"></i>Recent Members
                </h3>
                <a href="{{ route('td.members.index') }}" class="text-purple-600 text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentMembers as $member)
                <div class="flex items-center space-x-3 p-3 hover:bg-purple-50 rounded-lg transition cursor-pointer">
                    @if($member->profile_picture)
                        <img src="{{ asset('storage/' . $member->profile_picture) }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-purple-500 ring-offset-2" alt="{{ $member->full_name }}">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg ring-2 ring-purple-500 ring-offset-2">
                            <span class="text-white font-bold text-lg">{{ substr($member->full_name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $member->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $member->member_id }} • {{ $member->created_at->diffForHumans() }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-8">No recent members</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
@keyframes slide-right {
    0% { width: 0%; }
    100% { width: 100%; }
}
.animate-slide-right {
    animation: slide-right 5s ease-out forwards;
}
@keyframes slide-text {
    0% { left: 0%; opacity: 1; }
    95% { opacity: 1; }
    100% { left: 100%; opacity: 0; }
}
.animate-slide-text {
    animation: slide-text 5s ease-out forwards;
}
</style>
@endsection
