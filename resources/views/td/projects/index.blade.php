@extends('layouts.td')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Projects</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse($projects as $project)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-2">{{ $project->name }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($project->description, 100) }}</p>
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span>Progress</span>
                <span>{{ $project->progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
            </div>
        </div>
        <a href="{{ route('td.projects.show', $project->id) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-lg shadow p-6 text-center text-gray-500">No projects</div>
    @endforelse
</div>
<div class="mt-4">
    {{ $projects->links() }}
</div>
@endsection
