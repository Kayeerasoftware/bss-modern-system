@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-blue-600 to-cyan-600 p-2 md:p-4 rounded-xl shadow-xl">
                    <i class="fas fa-hand-holding-heart text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-1">Fundraising Campaigns</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">View and track fundraising campaigns</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4">
        <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-blue-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Campaigns...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-blue-100 text-[10px] font-medium mb-0.5">Active Campaigns</p>
            <h3 class="text-xl font-bold">0</h3>
        </div>
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-cyan-100 text-[10px] font-medium mb-0.5">Total Target</p>
            <h3 class="text-xl font-bold">UGX 0</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-green-100 text-[10px] font-medium mb-0.5">Total Raised</p>
            <h3 class="text-xl font-bold">UGX 0</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Completed</p>
            <h3 class="text-xl font-bold">0</h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-blue-600 to-cyan-600">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Campaign</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Target</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Raised</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Progress</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-hand-holding-heart text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-500 font-semibold">No campaigns found</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
</style>
@endsection
