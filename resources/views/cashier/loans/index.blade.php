@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-green-600 to-teal-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-hand-holding-usd text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 bg-clip-text text-transparent mb-1 md:mb-2">Loans Management</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">View and monitor all loan applications</p>
                </div>
            </div>
            <a href="{{ route('cashier.loans.create') }}" class="px-4 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:shadow-xl transition-all text-sm font-bold flex items-center gap-2 transform hover:scale-105">
                <i class="fas fa-plus"></i>Add Loan
            </a>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-green-500 to-teal-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-green-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Loans data...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 md:mb-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Loans</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $loans->total() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg">
                    <i class="fas fa-hand-holding-usd text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-[8px] md:text-[10px] font-medium mb-0.5">Pending</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $loans->where('status', 'pending')->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg">
                    <i class="fas fa-clock text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[8px] md:text-[10px] font-medium mb-0.5">Approved</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $loans->where('status', 'approved')->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg">
                    <i class="fas fa-check-circle text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-teal-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Amount</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($loans->sum('amount')/1000000, 1) }}M</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg">
                    <i class="fas fa-money-bill-wave text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-50 via-teal-50 to-blue-50 rounded-2xl shadow-lg border border-green-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('cashier.loans.index') }}" x-data="{ showAdvanced: false }">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-green-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-green-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search loans..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-circle absolute left-2.5 top-1/2 transform -translate-y-1/2 text-teal-400 text-[8px]"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-teal-200 rounded-lg focus:ring-2 focus:ring-teal-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort absolute left-2.5 top-1/2 transform -translate-y-1/2 text-blue-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg hover:shadow-md transition flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:shadow-lg transition font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('cashier.loans.index') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-green-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div class="flex gap-1">
                                <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white" @change="$el.form.submit()">
                                <input type="number" name="amount_max" value="{{ request('amount_max') }}" placeholder="Max" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white" @change="$el.form.submit()">
                            </div>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y-0">
                <thead class="bg-gradient-to-r from-green-600 via-teal-600 to-green-600">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Loan ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Member</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($loans as $loan)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-green-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-green-50 hover:to-teal-50 border-l-4 border-green-500">
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200 text-sm font-bold text-gray-900">{{ $loan->loan_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="flex items-center gap-3">
                                @if($loan->member)
                                @if($loan->member->profile_picture)
                                    <img src="{{ asset('storage/' . $loan->member->profile_picture) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-green-500 ring-offset-2">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center ring-2 ring-green-500 ring-offset-2">
                                        <span class="text-white font-bold">{{ substr($loan->member->full_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $loan->member->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $loan->member->member_id }}</p>
                                </div>
                                @else
                                <span class="text-sm text-gray-500">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200 text-sm font-bold text-green-600">{{ number_format($loan->amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full 
                                {{ $loan->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $loan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $loan->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('cashier.loans.show', $loan->id) }}" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition inline-block">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-hand-holding-usd text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No loans found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $loans->links() }}</div>
    </div>
</div>

<style>
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
tr { position: relative; }
tr::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, #10b981, #14b8a6, transparent);
    opacity: 0.3;
}
tr:hover::after { opacity: 0.6; }
</style>
@endsection
