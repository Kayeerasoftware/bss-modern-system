@extends('layouts.td')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">{{ $project->name }}</h2>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Project Details</h3>
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-gray-600">Budget:</span>
            <span class="font-bold">UGX {{ number_format($project->budget) }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Progress:</span>
            <span class="font-bold">{{ $project->progress }}%</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Status:</span>
            <span class="font-bold">{{ ucfirst($project->status) }}</span>
        </div>
    </div>
</div>
@endsection
