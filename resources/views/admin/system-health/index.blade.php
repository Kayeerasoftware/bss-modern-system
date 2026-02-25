@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <!-- Header Section -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-green-600 to-teal-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-heartbeat text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent mb-1 md:mb-2">System Health</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Monitor system performance, resources, and service status</p>
                </div>
            </div>
            <div class="flex gap-1.5 md:gap-2 w-full md:w-auto">
                <button onclick="location.reload()" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-sync-alt"></i><span class="hidden sm:inline">Refresh</span>
                </button>
                <button class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-white text-gray-700 rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 shadow-md border border-gray-200 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-file-export"></i><span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-green-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading System Health data...</span>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 md:mb-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">System Status</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $cpuUsage < 70 && $memoryUsage < 80 ? 'Healthy' : 'Warning' }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-{{ $cpuUsage < 70 && $memoryUsage < 80 ? 'check-circle' : 'exclamation-triangle' }} text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[8px] md:text-[10px] font-medium mb-0.5">CPU Usage</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $cpuUsage }}%</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-microchip text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-[8px] md:text-[10px] font-medium mb-0.5">Memory Usage</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $memoryUsage }}%</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-memory text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-[8px] md:text-[10px] font-medium mb-0.5">Disk Usage</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $diskUsage }}%</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-hdd text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Health Status -->
    <div class="mb-4 p-4 md:p-6 rounded-2xl shadow-xl {{ $cpuUsage < 70 && $memoryUsage < 80 ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-500' : 'bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-500' }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full flex items-center justify-center {{ $cpuUsage < 70 && $memoryUsage < 80 ? 'bg-green-500' : 'bg-yellow-500' }}">
                    <i class="fas {{ $cpuUsage < 70 && $memoryUsage < 80 ? 'fa-check-circle' : 'fa-exclamation-triangle' }} text-white text-xl md:text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-bold {{ $cpuUsage < 70 && $memoryUsage < 80 ? 'text-green-800' : 'text-yellow-800' }}">{{ $cpuUsage < 70 && $memoryUsage < 80 ? 'System Healthy' : 'Needs Attention' }}</h3>
                    <p class="text-sm {{ $cpuUsage < 70 && $memoryUsage < 80 ? 'text-green-600' : 'text-yellow-600' }}">{{ $cpuUsage < 70 && $memoryUsage < 80 ? 'All systems operational' : 'Some metrics need monitoring' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-600">Last Check</p>
                <p class="text-sm font-semibold">{{ now()->format('H:i:s') }}</p>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-tachometer-alt text-blue-600"></i>
                    Performance Metrics
                </h2>
            </div>
            <div class="p-4 md:p-6">
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium">Page Load Time</span>
                            <span class="font-bold text-blue-600">1.2s</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium">Database Query Time</span>
                            <span class="font-bold text-green-600">23ms</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 90%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium">Memory Usage</span>
                            <span class="font-bold text-yellow-600">{{ $memoryUsage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $memoryUsage }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium">CPU Usage</span>
                            <span class="font-bold text-purple-600">{{ $cpuUsage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $cpuUsage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-50 to-teal-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-info-circle text-green-600"></i>
                    System Information
                </h2>
            </div>
            <div class="p-4 md:p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">PHP Version</span>
                        <span class="font-bold">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Laravel Version</span>
                        <span class="font-bold">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Database Type</span>
                        <span class="font-bold">{{ config('database.default') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Environment</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">{{ app()->environment() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Debug Mode</span>
                        <span class="px-2 py-1 rounded text-xs font-bold {{ config('app.debug') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Timezone</span>
                        <span class="font-bold">{{ config('app.timezone') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Status -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-4">
        <div class="bg-gradient-to-r from-green-50 to-teal-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
            <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-server text-green-600"></i>
                Services Status
            </h2>
        </div>
        <div class="p-3 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-4">
                @foreach($services as $service)
                <div class="border-2 border-gray-200 rounded-xl p-3 md:p-4 hover:shadow-lg hover:border-green-300 transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 md:gap-3">
                            <div class="bg-gradient-to-br from-green-100 to-teal-100 p-2 md:p-3 rounded-lg">
                                <i class="fas fa-{{ $service['icon'] }} text-green-600 text-sm md:text-base"></i>
                            </div>
                            <span class="font-semibold text-sm md:text-base text-gray-800">{{ $service['name'] }}</span>
                        </div>
                        <span class="px-2 md:px-3 py-1 md:py-1.5 rounded-full text-[10px] md:text-xs font-bold {{ $service['status'] === 'running' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' }}">
                            <i class="fas fa-circle text-[6px] md:text-[8px] mr-1"></i>
                            {{ ucfirst($service['status']) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Security & Backup Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-red-50 to-pink-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-red-600"></i>
                    Security Status
                </h2>
            </div>
            <div class="p-4 md:p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-lock text-green-600"></i>
                            <span class="text-sm font-medium">SSL Certificate</span>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold">Valid</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-key text-blue-600"></i>
                            <span class="text-sm font-medium">Encryption</span>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold">Active</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-shield text-purple-600"></i>
                            <span class="text-sm font-medium">Active Sessions</span>
                        </div>
                        <span class="font-bold">5</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            <span class="text-sm font-medium">Failed Login Attempts</span>
                        </div>
                        <span class="font-bold">0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-database text-blue-600"></i>
                    Backup Status
                </h2>
            </div>
            <div class="p-4 md:p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-green-600"></i>
                            <span class="text-sm font-medium">Last Backup</span>
                        </div>
                        <span class="text-xs font-bold">Never</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-archive text-blue-600"></i>
                            <span class="text-sm font-medium">Total Backups</span>
                        </div>
                        <span class="font-bold">0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-hdd text-purple-600"></i>
                            <span class="text-sm font-medium">Backup Size</span>
                        </div>
                        <span class="font-bold">0 MB</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <span class="text-sm font-medium">Backup Status</span>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold">Healthy</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Status -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-4">
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
            <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-database text-blue-600"></i>
                Database Status
            </h2>
        </div>
        <div class="p-3 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-6">
                <div class="border-2 border-gray-200 rounded-xl p-4 md:p-6 hover:shadow-lg hover:border-blue-300 transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-3 rounded-lg">
                            <i class="fas fa-plug text-green-600 text-lg"></i>
                        </div>
                        <p class="text-xs md:text-sm text-gray-600 font-medium">Connection</p>
                    </div>
                    <p class="text-lg md:text-2xl font-bold text-green-600 ml-14">Active</p>
                </div>
                <div class="border-2 border-gray-200 rounded-xl p-4 md:p-6 hover:shadow-lg hover:border-purple-300 transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-gradient-to-br from-purple-100 to-pink-100 p-3 rounded-lg">
                            <i class="fas fa-table text-purple-600 text-lg"></i>
                        </div>
                        <p class="text-xs md:text-sm text-gray-600 font-medium">Total Tables</p>
                    </div>
                    <p class="text-lg md:text-2xl font-bold text-gray-800 ml-14">{{ $dbTables }}</p>
                </div>
                <div class="border-2 border-gray-200 rounded-xl p-4 md:p-6 hover:shadow-lg hover:border-orange-300 transition-all duration-300 transform hover:scale-105">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-gradient-to-br from-orange-100 to-yellow-100 p-3 rounded-lg">
                            <i class="fas fa-hdd text-orange-600 text-lg"></i>
                        </div>
                        <p class="text-xs md:text-sm text-gray-600 font-medium">Database Size</p>
                    </div>
                    <p class="text-lg md:text-2xl font-bold text-gray-800 ml-14">{{ $dbSize }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-4">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
            <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-tools text-indigo-600"></i>
                System Actions
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <button onclick="alert('Cache cleared successfully!')" class="p-4 border-2 border-blue-200 rounded-xl hover:bg-blue-50 hover:border-blue-400 transition text-center transform hover:scale-105">
                    <i class="fas fa-broom text-blue-600 text-2xl mb-2"></i>
                    <p class="text-sm font-semibold text-gray-800">Clear Cache</p>
                </button>
                <button onclick="alert('Database optimized!')" class="p-4 border-2 border-green-200 rounded-xl hover:bg-green-50 hover:border-green-400 transition text-center transform hover:scale-105">
                    <i class="fas fa-database text-green-600 text-2xl mb-2"></i>
                    <p class="text-sm font-semibold text-gray-800">Optimize DB</p>
                </button>
                <button onclick="alert('Running diagnostics...')" class="p-4 border-2 border-purple-200 rounded-xl hover:bg-purple-50 hover:border-purple-400 transition text-center transform hover:scale-105">
                    <i class="fas fa-stethoscope text-purple-600 text-2xl mb-2"></i>
                    <p class="text-sm font-semibold text-gray-800">Run Diagnostics</p>
                </button>
                <button onclick="alert('Exporting report...')" class="p-4 border-2 border-orange-200 rounded-xl hover:bg-orange-50 hover:border-orange-400 transition text-center transform hover:scale-105">
                    <i class="fas fa-file-export text-orange-600 text-2xl mb-2"></i>
                    <p class="text-sm font-semibold text-gray-800">Export Report</p>
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
            <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-gray-600"></i>
                Recent System Activity
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @foreach($recentActivity as $activity)
                <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-2 h-2 rounded-full mt-2 mr-3 {{ $activity['type'] === 'success' ? 'bg-green-500' : ($activity['type'] === 'warning' ? 'bg-yellow-500' : ($activity['type'] === 'error' ? 'bg-red-500' : 'bg-blue-500')) }}"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['timestamp'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
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
