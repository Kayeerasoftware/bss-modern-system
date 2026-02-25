@extends('layouts.td')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">{{ $member->name }}</h2>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-gray-600">Member ID:</span>
            <span class="font-medium">{{ $member->member_id }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Email:</span>
            <span class="font-medium">{{ $member->email }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Phone:</span>
            <span class="font-medium">{{ $member->phone }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Balance:</span>
            <span class="font-bold">UGX {{ number_format($member->balance ?? 0) }}</span>
        </div>
    </div>
</div>
@endsection
