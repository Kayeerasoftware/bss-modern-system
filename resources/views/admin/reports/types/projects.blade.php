<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Projects Report</h2>
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Projects</p>
            <p class="text-2xl font-bold text-blue-600">{{ $data->count() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Budget</p>
            <p class="text-2xl font-bold text-green-600">UGX {{ number_format($data->sum('budget') ?? 0) }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $data->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Completed</p>
            <p class="text-2xl font-bold text-purple-600">{{ $data->where('status', 'completed')->count() }}</p>
        </div>
    </div>
</div>

<table class="w-full">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Project Name</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Category</th>
            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">Budget</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Status</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Progress</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $project)
        <tr class="border-b">
            <td class="px-4 py-3">{{ $project->name ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ $project->category ?? 'N/A' }}</td>
            <td class="px-4 py-3 text-right">UGX {{ number_format($project->budget ?? 0) }}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs rounded-full {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($project->status ?? 'pending') }}
                </span>
            </td>
            <td class="px-4 py-3">{{ $project->progress ?? 0 }}%</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No projects found for this period</td>
        </tr>
        @endforelse
    </tbody>
</table>
