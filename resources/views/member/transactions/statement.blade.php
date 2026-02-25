@extends('layouts.member')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Account Statement</h2>
    <p class="text-gray-600">Download your account statement</p>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('member.statement.generate') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="from_date" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="to_date" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Generate</button>
            </div>
        </div>
    </form>
</div>
@endsection

