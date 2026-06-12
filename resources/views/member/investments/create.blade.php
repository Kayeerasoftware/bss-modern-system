@extends('layouts.member')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">New Investment</h2>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('shareholder.investments.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                <select name="project_id" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name ?? 'Project' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Investment Amount</label>
                <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Invest Now</button>
        </div>
    </form>
</div>
@endsection

