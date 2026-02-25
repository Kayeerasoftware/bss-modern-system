@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Transfers</h2>
    <p class="text-gray-600">Member-to-member transfers</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($transfers as $transfer)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->fromMember->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->toMember->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-bold">UGX {{ number_format($transfer->amount) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No transfers found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
