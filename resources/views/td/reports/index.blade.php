@extends('layouts.td')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Reports</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-2">Project Reports</h3>
        <p class="text-gray-600 text-sm mb-4">View project progress and status</p>
        <a href="{{ route('td.reports.projects') }}" class="text-blue-600 hover:text-blue-900">View Report</a>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-2">Member Reports</h3>
        <p class="text-gray-600 text-sm mb-4">Member statistics and analytics</p>
        <a href="{{ route('td.reports.members') }}" class="text-blue-600 hover:text-blue-900">View Report</a>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-2">Loan Reports</h3>
        <p class="text-gray-600 text-sm mb-4">Loan performance analysis</p>
        <a href="{{ route('td.reports.loans') }}" class="text-blue-600 hover:text-blue-900">View Report</a>
    </div>
</div>
@endsection
