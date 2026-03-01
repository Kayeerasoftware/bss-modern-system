@extends('layouts.td')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-3xl font-bold text-gray-800">Member Report</h2>
    <a href="{{ route('td.reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Back</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($members as $member)
                @php
                    $status = 'Active';
                    $statusClass = 'bg-green-100 text-green-800';
                    if (!$member->is_active) {
                        $status = 'Inactive';
                        $statusClass = 'bg-yellow-100 text-yellow-800';
                    }
                    if ($member->member && $member->member->trashed()) {
                        $status = 'Deleted Member Record';
                        $statusClass = 'bg-red-100 text-red-800';
                    } elseif (!$member->member) {
                        $status = 'No Member Record';
                        $statusClass = 'bg-gray-100 text-gray-700';
                    }
                @endphp
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $member->member?->member_id ?? 'N/A' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $member->name }}</td>
                    <td class="px-4 py-3">{{ $member->email }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ ucfirst((string) $member->role) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">{{ $status }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">No member records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
