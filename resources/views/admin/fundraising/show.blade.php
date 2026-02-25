@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.fundraising.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-purple-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-orange-600 bg-clip-text text-transparent">Campaign Details</h2>
                <p class="text-gray-600 text-sm">{{ $fundraising->campaign_id }}</p>
            </div>
        </div>
        <a href="{{ route('admin.fundraising.edit', $fundraising->id) }}" class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        <!-- Left Column - Campaign Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-orange-600 p-6 text-center">
                    <div class="w-24 h-24 rounded-full bg-white mx-auto flex items-center justify-center ring-4 ring-white shadow-xl">
                        <i class="fas fa-hand-holding-heart text-purple-600 text-4xl"></i>
                    </div>
                    <h3 class="text-white text-xl font-bold mt-4">{{ $fundraising->title }}</h3>
                    <p class="text-white/80 text-sm">{{ $fundraising->campaign_id }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="p-4 bg-blue-50 rounded-xl border-2 border-blue-200">
                        <p class="text-xs text-blue-600 font-bold mb-1">Total Contributions</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $fundraising->contributions->count() }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-xl border-2 border-green-200">
                        <p class="text-xs text-green-600 font-bold mb-1">Amount Raised</p>
                        <p class="text-xl font-bold text-green-600">UGX {{ number_format($fundraising->raised_amount, 0) }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl border-2 border-purple-200">
                        <p class="text-xs text-purple-600 font-bold mb-1">Remaining</p>
                        <p class="text-xl font-bold text-purple-600">UGX {{ number_format($fundraising->target_amount - $fundraising->raised_amount, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-bullseye text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Target Amount</p>
                    <p class="text-lg font-bold">{{ number_format($fundraising->target_amount, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-chart-line text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Raised Amount</p>
                    <p class="text-lg font-bold">{{ number_format($fundraising->raised_amount, 0) }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                    <i class="fas fa-percentage text-2xl mb-2 opacity-80"></i>
                    <p class="text-xs opacity-80">Progress</p>
                    <p class="text-lg font-bold">{{ number_format($fundraising->progress_percentage, 1) }}%</p>
                </div>
            </div>

            <!-- Progress Bar Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-chart-bar"></i>
                        Campaign Progress
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between mb-3">
                        <span class="text-sm font-bold text-gray-700">Progress</span>
                        <span class="text-lg font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ number_format($fundraising->progress_percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-6 shadow-inner">
                        <div class="bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500 h-6 rounded-full transition-all duration-500 flex items-center justify-end pr-2" style="width: {{ min($fundraising->progress_percentage, 100) }}%">
                            @if($fundraising->progress_percentage > 10)
                            <span class="text-white text-xs font-bold">{{ number_format($fundraising->progress_percentage, 1) }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Information Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-pink-600 to-orange-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Campaign Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Campaign ID</p>
                            <p class="font-bold text-gray-900">{{ $fundraising->campaign_id }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Status</p>
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800 border-green-200',
                                    'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                                ];
                                $colorClass = $statusColors[$fundraising->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $colorClass }}">
                                {{ ucfirst($fundraising->status) }}
                            </span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Start Date</p>
                            <p class="font-bold text-gray-900">{{ $fundraising->start_date->format('M d, Y') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">End Date</p>
                            <p class="font-bold text-gray-900">{{ $fundraising->end_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-align-left"></i>
                        Campaign Description
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $fundraising->description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
