<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Transactions Report</h2>
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Transactions</p>
            <p class="text-2xl font-bold text-blue-600">{{ $data->count() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Deposits</p>
            <p class="text-2xl font-bold text-green-600">{{ $data->where('type', 'deposit')->count() }}</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Withdrawals</p>
            <p class="text-2xl font-bold text-red-600">{{ $data->where('type', 'withdrawal')->count() }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Total Volume</p>
            <p class="text-2xl font-bold text-purple-600">UGX {{ number_format($data->sum('amount') ?? 0) }}</p>
        </div>
    </div>
</div>

<table class="w-full">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">ID</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Date</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Member</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Type</th>
            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">Amount</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $transaction)
        <tr class="border-b">
            <td class="px-4 py-3">{{ $transaction->id ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ $transaction->created_at ? $transaction->created_at->format('M d, Y H:i') : 'N/A' }}</td>
            <td class="px-4 py-3">{{ $transaction->member->full_name ?? 'N/A' }}</td>
            <td class="px-4 py-3">{{ ucfirst($transaction->type ?? 'N/A') }}</td>
            <td class="px-4 py-3 text-right">UGX {{ number_format($transaction->amount ?? 0) }}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs rounded-full {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($transaction->status ?? 'pending') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No transactions found for this period</td>
        </tr>
        @endforelse
    </tbody>
</table>
