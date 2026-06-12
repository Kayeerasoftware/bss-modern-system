@extends('layouts.shareholder')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Investment Details</h2>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">{{ $investment->project->name ?? 'Project' }}</h3>
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-gray-600">Investment Amount:</span>
            <span class="font-bold">UGX {{ number_format($investment->amount) }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Investment Date:</span>
            <span class="font-medium">{{ $investment->created_at->format('M d, Y') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Current Returns:</span>
            <span class="font-bold text-green-600">UGX {{ number_format($investment->returns ?? 0) }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">ROI:</span>
            <span class="font-bold">{{ number_format($investment->roi ?? 0, 2) }}%</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Status:</span>
            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
        </div>
    </div>
</div>
@endsection
