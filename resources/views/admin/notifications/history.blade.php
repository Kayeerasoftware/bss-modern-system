@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Notification History</h2>
    <p class="text-gray-600">View sent notifications</p>
</div>

<div class="space-y-4">
    @forelse($history as $item)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold">{{ $item->title }}</h3>
        <p class="text-gray-600 text-sm mt-2">{{ $item->message }}</p>
        <p class="text-xs text-gray-500 mt-2">Sent: {{ $item->created_at->format('M d, Y H:i') }}</p>
    </div>
    @empty
    <p class="text-center text-gray-500">No history</p>
    @endforelse
</div>
@endsection
