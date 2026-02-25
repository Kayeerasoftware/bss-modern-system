@extends('layouts.member')

@section('title', 'Project Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-indigo-50 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('member.projects.my-projects') }}" class="p-2 bg-white rounded-lg shadow hover:shadow-lg transition">
                <i class="fas fa-arrow-left text-indigo-600"></i>
            </a>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-indigo-600 to-blue-600 p-3 rounded-xl shadow-xl">
                        <i class="fas fa-project-diagram text-white text-2xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
                    <p class="text-gray-600 text-sm">Project Details</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Project Info -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600"></i>
                    Project Information
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Description</label>
                        <p class="text-gray-900">{{ $project->description ?? 'No description available' }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-tasks text-indigo-600"></i>
                    Progress
                </h3>
                <div class="mb-2 flex justify-between">
                    <span class="text-sm font-semibold text-gray-600">Completion</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 h-4 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Status</h3>
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full 
                    {{ $project->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $project->status == 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $project->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>

            <!-- Budget Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Budget</h3>
                <p class="text-3xl font-bold text-indigo-600">UGX {{ number_format($project->budget) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
