@extends('layouts.td')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Members</h2>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Default Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Other Roles</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($members as $member)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $member->member_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $member->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $member->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $defaultRole = strtolower((string) ($member->role ?? 'client'));
                    @endphp
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst($defaultRole) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $allRoles = $member->roles_list ?? [];
                        $otherRoles = array_values(array_filter($allRoles, fn ($r) => strtolower((string) $r) !== $defaultRole));
                    @endphp
                    @if(!empty($otherRoles))
                        <div class="flex flex-wrap gap-1">
                            @foreach($otherRoles as $otherRole)
                                <span class="px-2 py-1 text-[10px] font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ ucfirst($otherRole) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-gray-500">None</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">{{ ucfirst($member->status) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('td.members.show', $member->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No members</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
