@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-6">
        <a href="{{ route('cashier.members.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Members
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <div class="text-center">
                    <img src="{{ $member->profile_picture_url }}" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-blue-500 object-cover">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $member->full_name }}</h2>
                    <p class="text-gray-600 mb-4">{{ $member->member_id }}</p>
                    <div class="space-y-2 text-left">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-phone text-blue-600"></i>
                            <span>{{ $member->contact }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-blue-600"></i>
                            <span>{{ $member->email ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-wallet text-green-600"></i>
                            <span class="font-bold">Balance: {{ number_format($member->balance ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Transaction History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($member->transactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $transaction->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $transaction->type == 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount) }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">No transactions</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
