@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <!-- Header Section -->
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-blue-600 to-indigo-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-exchange-alt text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-1 md:mb-2">Transfers</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">View member transfers</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-blue-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Transfers data...</span>
    </div>

    <!-- Transfers Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-blue-500 mr-1"></i>{{ $transaction->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($transaction->member && $transaction->member->profile_picture_url)
                                    <img src="{{ $transaction->member->profile_picture_url }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-500 ring-offset-2" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-blue-500 ring-offset-2">
                                        <span class="text-white font-bold text-sm">{{ substr($transaction->member->full_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->member->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->member->member_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-lg text-blue-600">UGX {{ number_format($transaction->amount) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ Str::limit($transaction->description ?? 'N/A', 50) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-blue-100 to-indigo-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-exchange-alt text-blue-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No transfers found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $transactions->links() }}
    </div>
</div>

<style>
@keyframes slide-right {
    0% { width: 0%; }
    100% { width: 100%; }
}
.animate-slide-right {
    animation: slide-right 5s ease-out forwards;
}
@keyframes slide-text {
    0% { left: 0%; opacity: 1; }
    95% { opacity: 1; }
    100% { left: 100%; opacity: 0; }
}
.animate-slide-text {
    animation: slide-text 5s ease-out forwards;
}
</style>
@endsection

