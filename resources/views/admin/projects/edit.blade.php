@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.projects.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-indigo-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Edit Project</h2>
            <p class="text-gray-600 text-sm">Update project information - {{ $project->name }}</p>
        </div>
    </div>

    <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" class="max-w-6xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-project-diagram text-indigo-600 text-6xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-xl font-bold">Edit Project</h3>
                <p class="text-white/80 text-sm">Update the project details below</p>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- Basic Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-info-circle text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Basic Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-project-diagram text-indigo-600"></i>
                                Project Name *
                            </label>
                            <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-align-left text-purple-600"></i>
                                Description *
                            </label>
                            <textarea name="description" rows="4" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-tag text-pink-600"></i>
                                Category *
                            </label>
                            <select name="category" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                                <option value="">Select Category</option>
                                <option value="infrastructure" {{ old('category', $project->category) == 'infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                                <option value="technology" {{ old('category', $project->category) == 'technology' ? 'selected' : '' }}>Technology</option>
                                <option value="real_estate" {{ old('category', $project->category) == 'real_estate' ? 'selected' : '' }}>Real Estate</option>
                                <option value="agriculture" {{ old('category', $project->category) == 'agriculture' ? 'selected' : '' }}>Agriculture</option>
                                <option value="manufacturing" {{ old('category', $project->category) == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="other" {{ old('category', $project->category) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-circle text-green-600 text-[8px]"></i>
                                Status *
                            </label>
                            <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm appearance-none bg-white">
                                <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-money-bill-wave text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Financial Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-wallet text-indigo-600"></i>
                                Budget *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">UGX</span>
                                <input type="number" name="budget" value="{{ old('budget', $project->budget) }}" required step="0.01" class="w-full pl-16 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-chart-line text-purple-600"></i>
                                Expected ROI (%)
                            </label>
                            <input type="number" name="roi" value="{{ old('roi', $project->roi) }}" step="0.01" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-percentage text-pink-600"></i>
                                Progress (%)
                            </label>
                            <input type="number" name="progress" value="{{ old('progress', $project->progress ?? 0) }}" min="0" max="100" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-pink-600 to-indigo-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-calendar text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Timeline</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-plus text-green-600"></i>
                                Start Date *
                            </label>
                            <input type="date" name="start_date" value="{{ old('start_date', $project->start_date) }}" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-calendar-check text-blue-600"></i>
                                End Date
                            </label>
                            <input type="date" name="end_date" value="{{ old('end_date', $project->end_date) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-clipboard-list text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Additional Details</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user-tie text-indigo-600"></i>
                                Project Manager
                            </label>
                            <input type="text" name="manager" value="{{ old('manager', $project->manager) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-map-marker-alt text-purple-600"></i>
                                Location
                            </label>
                            <input type="text" name="location" value="{{ old('location', $project->location) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-sticky-note text-yellow-600"></i>
                                Notes
                            </label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all text-sm">{{ old('notes', $project->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.projects.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Update Project
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
