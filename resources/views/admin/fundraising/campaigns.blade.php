@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Fundraising Campaigns</h2>
        <p class="text-gray-600">Manage fundraising activities</p>
    </div>
    <a href="{{ route('admin.fundraising.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>New Campaign
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($campaigns as $campaign)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-2">{{ $campaign->name }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($campaign->description, 80) }}</p>
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span>Raised</span>
                <span>{{ number_format(($campaign->raised / $campaign->goal) * 100, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(($campaign->raised / $campaign->goal) * 100, 100) }}%"></div>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm font-bold">UGX {{ number_format($campaign->raised) }} / {{ number_format($campaign->goal) }}</span>
            <a href="{{ route('admin.fundraising.show', $campaign->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-lg shadow p-6 text-center text-gray-500">No campaigns</div>
    @endforelse
</div>
@endsection
