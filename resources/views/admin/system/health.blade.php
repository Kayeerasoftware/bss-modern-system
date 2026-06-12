@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">System Health</h2>
    <p class="text-gray-600">Monitor system performance</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm text-gray-600 mb-2">CPU Usage</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $health['cpu'] ?? '0' }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm text-gray-600 mb-2">Memory Usage</h3>
        <p class="text-3xl font-bold text-green-600">{{ $health['memory'] ?? '0' }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm text-gray-600 mb-2">Disk Space</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $health['disk'] ?? '0' }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm text-gray-600 mb-2">Uptime</h3>
        <p class="text-3xl font-bold text-orange-600">{{ $health['uptime'] ?? '0' }}h</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">System Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Clear Cache</button>
        <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Optimize Database</button>
        <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Run Diagnostics</button>
    </div>
</div>
@endsection
