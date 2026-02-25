@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('shareholder.loans.applications') }}" class="p-2 bg-white rounded-lg shadow hover:shadow-md transition">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Loan Application Details</h1>
                    <p class="text-gray-600">Application ID: {{ $application->application_id }}</p>
                </div>
            </div>
        </div>

        <!-- Application Details Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Application Information</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount Requested</label>
                        <p class="text-lg font-bold text-gray-900">UGX {{ number_format($application->amount) }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Repayment Period</label>
                        <p class="text-lg font-bold text-gray-900">{{ $application->repayment_months ?? 'N/A' }} months</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                        <p class="text-gray-900">{{ $application->purpose }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Application Date</label>
                        <p class="text-gray-900">{{ $application->created_at->format('F d, Y') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interest Rate</label>
                        <p class="text-gray-900">{{ $application->interest_rate ?? '10.00' }}%</p>
                    </div>
                </div>
                
                @if($application->approval_comment)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Review Comment</label>
                    <p class="text-gray-900">{{ $application->approval_comment }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection