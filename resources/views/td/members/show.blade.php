@extends('layouts.td')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">{{ $member->name }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Profile</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Member ID:</span>
                <span class="font-medium">{{ $member->member?->member_id ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Email:</span>
                <span class="font-medium">{{ $member->email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Phone:</span>
                <span class="font-medium">{{ $member->phone ?? $member->member?->contact ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Default Role:</span>
                <span class="font-medium">{{ ucfirst((string) $member->role) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span class="font-medium">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Savings Balance:</span>
                <span class="font-bold">UGX {{ number_format((float) ($member->member?->savings_balance ?? 0), 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">General Balance:</span>
                <span class="font-bold">UGX {{ number_format((float) ($member->member?->balance ?? 0), 2) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Recent Loans</h3>
        <div class="space-y-3">
            @forelse(($member->member?->loans ?? collect())->take(5) as $loan)
                <div class="flex items-center justify-between border rounded-lg px-3 py-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $loan->loan_id }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst((string) $loan->status) }}</p>
                    </div>
                    <p class="text-sm font-bold text-gray-900">UGX {{ number_format((float) $loan->amount, 2) }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No loans found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
