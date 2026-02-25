@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <!-- Header Section -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-pink-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-red-600 to-pink-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-arrow-up text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-red-600 via-pink-600 to-rose-600 bg-clip-text text-transparent mb-1 md:mb-2">Withdrawals</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Manage member withdrawals and payouts</p>
                </div>
            </div>
            <div class="flex gap-1.5 md:gap-2 w-full md:w-auto">
                <button class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-plus"></i><span class="hidden sm:inline">New Withdrawal</span><span class="sm:hidden">New</span>
                </button>
                <button class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-white text-gray-700 rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 shadow-md border border-gray-200 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-file-export"></i><span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-red-500 to-pink-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-red-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Withdrawals data...</span>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-red-50 via-pink-50 to-rose-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fee</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Net Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 transition-all duration-200">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-red-500 mr-1"></i>{{ $withdrawal->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($withdrawal->member && $withdrawal->member->profile_picture)
                                    <img src="{{ asset('storage/' . $withdrawal->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-red-500 ring-offset-2" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center ring-2 ring-red-500 ring-offset-2">
                                        <span class="text-white font-bold text-sm">{{ substr($withdrawal->member->full_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $withdrawal->member->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $withdrawal->member->member_id ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-lg text-red-600">UGX {{ number_format($withdrawal->amount) }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-semibold text-orange-600">UGX {{ number_format($withdrawal->fee) }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-lg text-gray-900">UGX {{ number_format($withdrawal->net_amount) }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($withdrawal->status == 'completed')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check text-[8px] mr-1"></i>Completed
                                </span>
                            @elseif($withdrawal->status == 'pending')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <i class="fas fa-clock text-[8px] mr-1"></i>Pending
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-times text-[8px] mr-1"></i>{{ ucfirst($withdrawal->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-red-100 to-pink-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-arrow-up text-red-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No withdrawals found</p>
                                <p class="text-gray-400 text-sm mt-2">Withdrawals will appear here once created</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
