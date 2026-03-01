@extends('layouts.td')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Loan Details</h2>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-gray-600">Loan ID:</span>
            <span class="font-medium">{{ $loan->loan_id }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Member:</span>
            <span class="font-medium">{{ $loan->member->full_name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Amount:</span>
            <span class="font-bold">UGX {{ number_format((float) $loan->amount, 2) }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Interest Rate:</span>
            <span class="font-medium">{{ $loan->interest_rate }}%</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Duration:</span>
            <span class="font-medium">{{ $loan->duration }} months</span>
        </div>
    </div>
</div>
@endsection
