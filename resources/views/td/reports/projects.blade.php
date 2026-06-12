@extends('layouts.td')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-3xl font-bold text-gray-800">Project Report</h2>
    <a href="{{ route('td.reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Back</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($projects as $project)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $project->project_id }}</td>
                    <td class="px-4 py-3">{{ $project->name }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ ucfirst((string) $project->status) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ (int) $project->progress }}%</td>
                    <td class="px-4 py-3 whitespace-nowrap">UGX {{ number_format((float) ($project->budget ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">No project records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
