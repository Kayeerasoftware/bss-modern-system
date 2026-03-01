@extends('layouts.td')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-3xl font-bold text-gray-800">Loan Report</h2>
    <a href="{{ route('td.reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Back</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($loans as $loan)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $loan->loan_id }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $loan->member->full_name }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">UGX {{ number_format((float) $loan->amount, 2) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">UGX {{ number_format((float) $loan->amount_paid, 2) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">UGX {{ number_format((float) $loan->remaining_balance, 2) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $loan->status_label }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No loan records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
