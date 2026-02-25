<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Loans Report</h2>
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Loans</p>
            <p class="text-2xl font-bold text-blue-600">{{ $data->count() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Amount</p>
            <p class="text-2xl font-bold text-green-600">UGX {{ number_format($data->sum('amount') ?? 0) }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Approved</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $data->where('status', 'approved')->count() }}</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Pending</p>
            <p class="text-2xl font-bold text-red-600">{{ $data->where('status', 'pending')->count() }}</p>
        </div>
    </div>
</div>

<table class="w-full">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Loan ID</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Member</th>
            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">Amount</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Status</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $loan)
        <tr class="border-b">
            <td class="px-4 py-3">{{ $loan->id ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ $loan->member->full_name ?? 'N/A' }}</td>
            <td class="px-4 py-3 text-right">UGX {{ number_format($loan->amount ?? 0) }}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs rounded-full {{ $loan->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($loan->status ?? 'pending') }}
                </span>
            </td>
            <td class="px-4 py-3">{{ $loan->created_at ? $loan->created_at->format('M d, Y') : 'N/A' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No loans found for this period</td>
        </tr>
        @endforelse
    </tbody>
</table>
