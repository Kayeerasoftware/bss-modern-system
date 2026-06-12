@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-indigo-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Project Details</h2>
                <p class="text-gray-600 text-sm">{{ $project->name }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        <!-- Left Column - Project Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 text-center">
                    <div class="w-24 h-24 rounded-full bg-white mx-auto flex items-center justify-center ring-4 ring-white shadow-xl mb-4">
                        <i class="fas fa-project-diagram text-indigo-600 text-4xl"></i>
                    </div>
                    <h3 class="text-white text-xl font-bold">{{ $project->name }}</h3>
                    <p class="text-white/80 text-sm">{{ $project->category ?? 'N/A' }}</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-indigo-50 rounded-xl">
                        <i class="fas fa-circle text-indigo-600 text-xs"></i>
                        <div>
                            <p class="text-xs text-gray-600">Status</p>
                            @if($project->status == 'active')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-circle text-[6px] mr-1"></i>Active
                                </span>
                            @elseif($project->status == 'completed')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-check text-[6px] mr-1"></i>Completed
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ ucfirst($project->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-xl">
                        <i class="fas fa-wallet text-purple-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Budget</p>
                            <p class="font-semibold text-sm">UGX {{ number_format($project->budget ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-pink-50 rounded-xl">
                        <i class="fas fa-chart-line text-pink-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Expected ROI</p>
                            <p class="font-semibold text-sm">{{ number_format($project->roi ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Progress Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-tasks"></i>
                        Project Progress
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-700 font-semibold">Completion Status</span>
                        <span class="text-3xl font-bold text-indigo-600">{{ $project->progress ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-6 rounded-full flex items-center justify-end pr-2 transition-all duration-500" style="width: {{ $project->progress ?? 0 }}%">
                            <span class="text-white text-xs font-bold">{{ $project->progress ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-align-left"></i>
                        Description
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $project->description ?? 'No description available' }}</p>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-pink-600 to-indigo-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-calendar"></i>
                        Timeline
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                            <p class="text-xs text-gray-600 mb-1">Start Date</p>
                            <p class="font-bold text-green-600">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-200">
                            <p class="text-xs text-gray-600 mb-1">End Date</p>
                            <p class="font-bold text-blue-600">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            @if($project->manager || $project->location)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Additional Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        @if($project->manager)
                        <div class="p-4 bg-indigo-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Project Manager</p>
                            <p class="font-bold text-gray-900">{{ $project->manager }}</p>
                        </div>
                        @endif
                        @if($project->location)
                        <div class="p-4 bg-purple-50 rounded-xl">
                            <p class="text-xs text-gray-600 mb-1">Location</p>
                            <p class="font-bold text-gray-900">{{ $project->location }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($project->notes)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                    <h3 class="text-white text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-sticky-note"></i>
                        Notes
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $project->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, button, a[href*="edit"] {
        display: none !important;
    }
}
</style>
@endsection
