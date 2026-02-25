@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Approved Loans</h2>
    <p class="text-gray-600">All approved loans</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly Payment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($loans as $loan)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $loan->loan_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $loan->member->full_name ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-bold">{{ number_format($loan->amount, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($loan->monthly_payment, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $loan->updated_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.loans.show', $loan->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No approved loans</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $loans->links() }}
</div>
@endsection
