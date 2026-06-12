@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">System Backups</h2>
        <p class="text-gray-600">Manage database backups</p>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Create Backup
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($backups as $backup)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $backup->created_at->format('M d, Y H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $backup->size }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($backup->type) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <a href="{{ route('admin.backups.download', $backup->id) }}" class="text-blue-600 hover:text-blue-900">Download</a>
                    <button class="text-green-600 hover:text-green-900">Restore</button>
                    <button class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No backups found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
