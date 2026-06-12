@extends('layouts.member')

@section('title', 'Projects')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-indigo-50 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-3 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-indigo-600 to-blue-600 p-3 md:p-4 rounded-xl shadow-xl">
                    <i class="fas fa-project-diagram text-white text-2xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent mb-1">Projects</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">View organization projects and opportunities</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-600 rounded-full animate-slide-right"></div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-3 md:p-4 text-white">
            <i class="fas fa-folder-open text-2xl mb-2 opacity-80"></i>
            <p class="text-white/80 text-xs font-medium">Total Projects</p>
            <h3 class="text-xl md:text-3xl font-bold">{{ \App\Models\Project::count() }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500">
            <i class="fas fa-play-circle text-2xl text-green-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Active</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ \App\Models\Project::where('status', 'active')->count() }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500">
            <i class="fas fa-check-circle text-2xl text-blue-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Completed</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ \App\Models\Project::where('status', 'completed')->count() }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-yellow-500">
            <i class="fas fa-clock text-2xl text-yellow-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Pending</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ \App\Models\Project::where('status', 'pending')->count() }}</h3>
        </div>
    </div>

    <!-- Projects List -->
    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-list text-indigo-600"></i>
            All Projects
        </h3>

        <!-- Search and Filter -->
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl shadow-lg border border-indigo-100 mb-4">
            <form method="GET" action="{{ route('member.projects.my-projects') }}" class="p-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-6 relative">
                        <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                    </div>
                    <div class="md:col-span-3 relative">
                        <select name="status" class="w-full px-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="md:col-span-3 flex gap-1.5">
                        <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-semibold">
                            <i class="fas fa-search mr-1"></i>Search
                        </button>
                        <a href="{{ route('member.projects.my-projects') }}" class="px-2 py-1.5 text-xs bg-gray-200 text-gray-700 rounded-lg">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse(\App\Models\Project::latest()->paginate(9) as $project)
            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-xl transition transform hover:scale-105">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i class="fas fa-project-diagram text-indigo-600 text-xl"></i>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                        {{ $project->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $project->status == 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $project->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
                <h4 class="font-bold text-gray-900 mb-2 text-lg">{{ $project->name }}</h4>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description ?? 'No description available' }}</p>
                
                <div class="space-y-2 mb-3">
                    @if(isset($project->budget))
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Budget:</span>
                        <span class="font-semibold text-indigo-600">UGX {{ number_format($project->budget) }}</span>
                    </div>
                    @endif
                    @if(isset($project->progress))
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Progress</span>
                            <span class="font-semibold">{{ $project->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <a href="{{ route('member.projects.show', $project->id) }}" class="block w-full text-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg transition">
                    <i class="fas fa-eye mr-1"></i>View Details
                </a>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-folder-open text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-lg font-medium">No projects available</p>
                <p class="text-gray-400 text-sm">Check back later for new opportunities</p>
            </div>
            @endforelse
        </div>

        @if(\App\Models\Project::count() > 9)
        <div class="mt-6">
            {{ \App\Models\Project::latest()->paginate(9)->links() }}
        </div>
        @endif
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection
