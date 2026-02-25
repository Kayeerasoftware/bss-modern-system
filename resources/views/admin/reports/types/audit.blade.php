<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Audit Report</h2>
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Activities</p>
            <p class="text-2xl font-bold text-blue-600">{{ $data->count() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Unique Users</p>
            <p class="text-2xl font-bold text-green-600">{{ $data->unique('user_id')->count() }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Actions</p>
            <p class="text-2xl font-bold text-purple-600">{{ $data->unique('action')->count() }}</p>
        </div>
    </div>
</div>

<table class="w-full">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Date</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">User</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Action</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Description</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $log)
        <tr class="border-b">
            <td class="px-4 py-3">{{ $log->created_at ? $log->created_at->format('M d, Y H:i') : 'N/A' }}</td>
            <td class="px-4 py-3">{{ $log->user->name ?? 'System' }}</td>
            <td class="px-4 py-3">{{ $log->action ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ $log->description ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No audit logs found for this period</td>
        </tr>
        @endforelse
    </tbody>
</table>
