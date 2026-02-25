@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Loan Applications</h2>
        <p class="text-gray-600">Review and process pending loan applications</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.loans.approvals') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-check-circle mr-2"></i>Approved Loans
        </a>
        <a href="{{ route('admin.loans.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-list mr-2"></i>All Loans
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Pending Applications</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $loans->total() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Total Amount Requested</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($loans->sum('amount'), 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Avg. Loan Amount</p>
        <p class="text-2xl font-bold text-purple-600">{{ $loans->count() > 0 ? number_format($loans->avg('amount'), 2) : '0.00' }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interest</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Repayment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($loans as $loan)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $loan->loan_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($loan->member && $loan->member->profile_picture)
                            <img src="{{ asset('storage/' . $loan->member->profile_picture) }}" class="w-8 h-8 rounded-full mr-2" alt="">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gray-300 mr-2 flex items-center justify-center">
                                <span class="text-xs">{{ $loan->member ? substr($loan->member->full_name, 0, 1) : 'N' }}</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium">{{ $loan->member->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $loan->member->member_id ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-bold text-blue-600">{{ number_format($loan->amount, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($loan->interest, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-bold">{{ number_format($loan->amount + $loan->interest, 2) }}</td>
                <td class="px-6 py-4">{{ Str::limit($loan->purpose, 30) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loan->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.loans.show', $loan->id) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900" title="Approve">
                                <i class="fas fa-check-circle"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Reject this loan?')" title="Reject">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                    <p>No pending loan applications</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $loans->links() }}
</div>
@endsection
