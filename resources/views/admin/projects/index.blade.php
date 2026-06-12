@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-indigo-600 to-purple-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-project-diagram text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-1 md:mb-2">Investment Projects</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Manage and track all investment projects</p>
                </div>
            </div>
            <div class="flex gap-1.5 md:gap-2 w-full md:w-auto">
                <a href="{{ route('admin.projects.create') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-plus"></i><span>New Project</span>
                </a>
                <button class="px-3 md:px-5 py-2 md:py-2.5 bg-white text-gray-700 rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 shadow-md border border-gray-200 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-file-export"></i><span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-indigo-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Projects data...</span>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 md:mb-4">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Projects</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $projects->total() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-project-diagram text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">Active</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $projects->where('status', 'active')->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-check-circle text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Budget</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($projects->sum('budget'), 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-money-bill-wave text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-[8px] md:text-[10px] font-medium mb-0.5">Avg Progress</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($projects->avg('progress'), 1) }}%</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-chart-line text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-2xl shadow-lg border border-indigo-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('admin.projects.index') }}">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-indigo-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all appearance-none bg-white">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-calendar absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-calendar absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                        </div>
                        <div class="md:col-span-2 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.projects.index') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                            <button type="button" onclick="toggleView('table')" id="tableViewBtn" class="px-2 py-1.5 text-xs bg-indigo-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold">
                                <i class="fas fa-table"></i>
                            </button>
                            <button type="button" onclick="toggleView('grid')" id="gridViewBtn" class="px-2 py-1.5 text-xs bg-white text-gray-700 rounded-lg hover:shadow-lg transition-all border border-gray-200 font-semibold">
                                <i class="fas fa-th"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Table View -->
    <div id="tableView" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Project</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Budget</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ROI</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Timeline</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($projects as $project)
                    <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center ring-2 ring-indigo-500 ring-offset-2">
                                    <i class="fas fa-project-diagram text-white text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Str::limit($project->description ?? 'N/A', 40) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
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
                                    <i class="fas fa-pause text-[6px] mr-1"></i>{{ ucfirst($project->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="w-32">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-600">{{ $project->progress ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-2 rounded-full" style="width: {{ $project->progress ?? 0 }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-indigo-600">UGX {{ number_format($project->budget ?? 0, 0) }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-purple-600">{{ number_format($project->roi ?? 0, 1) }}%</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-calendar text-indigo-500 text-xs"></i>
                                <span>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d') : 'N/A' }}</span>
                                <span>-</span>
                                <span>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-all transform hover:scale-110" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="p-2 text-yellow-600 hover:bg-yellow-100 rounded-lg transition-all transform hover:scale-110" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all transform hover:scale-110" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-indigo-100 to-purple-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-project-diagram text-indigo-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No projects found</p>
                                <p class="text-gray-400 text-sm mt-2">Create your first project to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid View -->
    <div id="gridView" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($projects as $project)
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 transform hover:scale-105 transition-all duration-300">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 text-center relative">
                <div class="absolute top-3 right-3">
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
                            <i class="fas fa-pause text-[6px] mr-1"></i>{{ ucfirst($project->status) }}
                        </span>
                    @endif
                </div>
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center shadow-2xl">
                        <i class="fas fa-project-diagram text-indigo-600 text-4xl"></i>
                    </div>
                </div>
                <h3 class="text-white text-lg font-bold">{{ $project->name }}</h3>
            </div>
            
            <div class="p-6 space-y-4">
                <p class="text-gray-600 text-sm line-clamp-2">{{ $project->description ?? 'No description' }}</p>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 font-semibold">Progress</span>
                        <span class="text-indigo-600 font-bold">{{ $project->progress ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ $project->progress ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-indigo-50 rounded-xl border border-indigo-200">
                        <p class="text-xs text-gray-600 mb-1">Budget</p>
                        <p class="text-sm font-bold text-indigo-600">UGX {{ number_format($project->budget ?? 0, 0) }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-xl border border-purple-200">
                        <p class="text-xs text-gray-600 mb-1">ROI</p>
                        <p class="text-sm font-bold text-purple-600">{{ number_format($project->roi ?? 0, 1) }}%</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-calendar"></i>
                    <span>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'N/A' }}</span>
                    <span>-</span>
                    <span>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : 'N/A' }}</span>
                </div>

                <div class="flex gap-2 pt-4 border-t">
                    <a href="{{ route('admin.projects.show', $project->id) }}" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold text-center">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="{{ route('admin.projects.edit', $project->id) }}" class="flex-1 px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold text-center">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this project?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white rounded-2xl shadow-xl p-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="bg-gradient-to-br from-indigo-100 to-purple-100 p-6 rounded-full mb-4">
                    <i class="fas fa-project-diagram text-indigo-400 text-5xl"></i>
                </div>
                <p class="text-gray-500 text-lg font-semibold">No projects found</p>
                <p class="text-gray-400 text-sm mt-2">Create your first project to get started</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $projects->links() }}
    </div>
</div>

<script>
function toggleView(view) {
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');
    const tableBtn = document.getElementById('tableViewBtn');
    const gridBtn = document.getElementById('gridViewBtn');
    
    if (view === 'table') {
        tableView.classList.remove('hidden');
        gridView.classList.add('hidden');
        tableBtn.classList.add('bg-indigo-600', 'text-white');
        tableBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
        gridBtn.classList.remove('bg-indigo-600', 'text-white');
        gridBtn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
    } else {
        tableView.classList.add('hidden');
        gridView.classList.remove('hidden');
        gridBtn.classList.add('bg-indigo-600', 'text-white');
        gridBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
        tableBtn.classList.remove('bg-indigo-600', 'text-white');
        tableBtn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
    }
}
</script>

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
