@extends('layouts.shareholder')

@section('title', 'Shareholder Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-3 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-chart-pie text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 bg-clip-text text-transparent mb-1 md:mb-2">Shareholder Dashboard</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Welcome, {{ auth()->user()->name ?? 'Shareholder' }} - Monitor your investment portfolio</p>
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ route('shareholder.bio-data.view') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2">
                    <i class="fas fa-eye"></i><span>View Bio Data</span>
                </a>
                <a href="{{ route('shareholder.bio-data.create') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2">
                    <i class="fas fa-id-card"></i><span>Edit Bio Data</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Portfolio data...</span>
    </div>

    <!-- Key Metrics -->
    @include('shareholder.partials.stats-cards')

    <!-- Charts Section with Tabs -->
    @include('shareholder.partials.charts')

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Recent Dividends -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:shadow-2xl transition border border-gray-100">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-hand-holding-usd text-purple-600 mr-2 text-sm md:text-base"></i>
                    <span class="text-sm md:text-base">Recent Dividends</span>
                </h3>
                <a href="{{ route('shareholder.dividends.index') }}" class="text-blue-600 text-xs md:text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-2 md:space-y-3">
                @forelse($recentDividends as $dividend)
                <div class="flex items-center justify-between p-2 md:p-3 hover:bg-purple-50 rounded-lg transition cursor-pointer">
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-semibold text-gray-900">Dividend Payment</p>
                        <p class="text-[10px] md:text-xs text-gray-500">{{ $dividend->payment_date->format('M d, Y') }}</p>
                    </div>
                    <p class="text-xs md:text-sm font-bold text-green-600">+{{ number_format($dividend->amount, 0) }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-xs md:text-sm text-center py-6 md:py-8">No recent dividends</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:shadow-2xl transition border border-gray-100">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-project-diagram text-indigo-600 mr-2 text-sm md:text-base"></i>
                    <span class="text-sm md:text-base">Recent Projects</span>
                </h3>
                <a href="{{ route('shareholder.projects.index') }}" class="text-blue-600 text-xs md:text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-2 md:space-y-3">
                @forelse($recentProjects as $project)
                <div class="flex items-center justify-between p-2 md:p-3 hover:bg-indigo-50 rounded-lg transition cursor-pointer">
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-semibold text-gray-900 truncate">{{ $project->name }}</p>
                        <p class="text-[10px] md:text-xs text-gray-500">ROI: {{ number_format($project->roi ?? 0, 1) }}%</p>
                    </div>
                    <span class="px-2 md:px-3 py-1 text-[10px] md:text-xs rounded-full font-semibold {{ $project->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-xs md:text-sm text-center py-6 md:py-8">No recent projects</p>
                @endforelse
            </div>
        </div>

        <!-- Investment Opportunities -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 hover:shadow-2xl transition border border-gray-100">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-600 mr-2 text-sm md:text-base"></i>
                    <span class="text-sm md:text-base">Opportunities</span>
                </h3>
                <a href="{{ route('shareholder.investments.index') }}" class="text-blue-600 text-xs md:text-sm hover:underline font-semibold">View All →</a>
            </div>
            <div class="space-y-2 md:space-y-3">
                @forelse($investmentOpportunities as $opportunity)
                <div class="flex items-center justify-between p-2 md:p-3 hover:bg-yellow-50 rounded-lg transition cursor-pointer">
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-semibold text-gray-900 truncate">{{ $opportunity->name ?? 'Investment' }}</p>
                        <p class="text-[10px] md:text-xs text-gray-500">Expected ROI: {{ number_format($opportunity->expected_roi ?? 0, 1) }}%</p>
                    </div>
                    <span class="px-2 md:px-3 py-1 text-[10px] md:text-xs rounded-full font-semibold bg-blue-100 text-blue-800">
                        {{ ucfirst($opportunity->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-xs md:text-sm text-center py-6 md:py-8">No opportunities</p>
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

@push('scripts')
@include('shareholder.partials.scripts')
@endpush
