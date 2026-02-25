<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Members Report</h2>
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Members</p>
            <p class="text-2xl font-bold text-blue-600">{{ $data->count() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Balance</p>
            <p class="text-2xl font-bold text-green-600">UGX {{ number_format($data->sum('balance') ?? 0) }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Average Balance</p>
            <p class="text-2xl font-bold text-purple-600">UGX {{ number_format($data->avg('balance') ?? 0) }}</p>
        </div>
    </div>
</div>

<table class="w-full">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Member ID</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Name</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Email</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Phone</th>
            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $member)
        <tr class="border-b">
            <td class="px-4 py-3">{{ $member->member_id ?? $member->id }}</td>
            <td class="px-4 py-3">{{ $member->full_name }}</td>
            <td class="px-4 py-3">{{ $member->email ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ $member->contact ?? 'N/A' }}</td>
            <td class="px-4 py-3 text-right">UGX {{ number_format($member->balance ?? 0) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No members found for this period</td>
        </tr>
        @endforelse
    </tbody>
</table>
