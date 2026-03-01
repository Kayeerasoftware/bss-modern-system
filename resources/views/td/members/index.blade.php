@extends('layouts.td')

@section('content')
<div class="mb-6 flex flex-col gap-4">
    <h2 class="text-3xl font-bold text-gray-800">Members</h2>
    <form method="GET" class="bg-white rounded-lg shadow p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by name, email, member ID..."
            class="px-3 py-2 border rounded-lg"
        >
        <select name="role" class="px-3 py-2 border rounded-lg">
            <option value="">All Roles</option>
            @foreach(['admin', 'td', 'ceo', 'treasurer', 'secretary', 'loan_officer', 'shareholder', 'client'] as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border rounded-lg">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            <option value="deleted" @selected(request('status') === 'deleted')>Deleted Member Record</option>
            <option value="unlinked" @selected(request('status') === 'unlinked')>No Member Record</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('td.members.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
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
            @php
                $linkedMember = $member->member;
                $defaultRole = strtolower((string) ($member->role ?? 'client'));
                $allRoles = $member->roles_list ?? [];
                $otherRoles = array_values(array_filter($allRoles, fn ($r) => strtolower((string) $r) !== $defaultRole));
                $memberState = 'Active';
                $memberStateClass = 'bg-green-100 text-green-800';

                if (!$member->is_active) {
                    $memberState = 'Inactive';
                    $memberStateClass = 'bg-yellow-100 text-yellow-800';
                }

                if ($linkedMember && $linkedMember->trashed()) {
                    $memberState = 'Deleted Member Record';
                    $memberStateClass = 'bg-red-100 text-red-800';
                } elseif (!$linkedMember) {
                    $memberState = 'No Member Record';
                    $memberStateClass = 'bg-gray-100 text-gray-700';
                }
            @endphp
            <tr class="{{ $linkedMember && $linkedMember->trashed() ? 'bg-red-50' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap">{{ $linkedMember?->member_id ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $member->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $member->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst($defaultRole) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
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
                    <span class="px-2 py-1 text-xs rounded-full {{ $memberStateClass }}">{{ $memberState }}</span>
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

<div class="mt-4">
    {{ $members->links() }}
</div>
@endsection
