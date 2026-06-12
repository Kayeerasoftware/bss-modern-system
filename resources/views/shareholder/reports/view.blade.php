@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ ucfirst($type) }} Report</h1>
            <a href="{{ route('shareholder.reports.index') }}" class="px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        <p class="text-sm text-gray-600 mt-2">Period: {{ $from_date }} to {{ $to_date }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Report Data</h2>
            <button onclick="window.print()" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>

        @if($format === 'html')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-purple-50 to-pink-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Details</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(is_array($data) || $data instanceof \Illuminate\Support\Collection)
                            @forelse($data as $item)
                            <tr class="hover:bg-purple-50">
                                <td class="px-4 py-3 text-sm">{{ is_object($item) && isset($item->id) ? $item->id : 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">{{ is_object($item) && isset($item->created_at) ? $item->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">{{ is_object($item) ? ($item->description ?? $item->type ?? 'N/A') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm font-bold">UGX {{ is_object($item) && isset($item->amount) ? number_format($item->amount, 0) : '0' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No data available for this period</td>
                            </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No data available for this period</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600">CSV download will start automatically...</p>
        @endif
    </div>
</div>
@endsection
