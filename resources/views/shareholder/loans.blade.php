@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center justify-between gap-2 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-money-bill-wave text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">Loans Overview</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">View loan information and statistics</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('shareholder.loans', ['tab' => 'applications']) }}#loan-applications" class="px-3 md:px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:shadow-lg transition-all text-xs md:text-sm font-semibold">
                    <i class="fas fa-list mr-1 md:mr-2"></i>Applications
                </a>
                <a href="{{ route('shareholder.loans.apply') }}" class="px-3 md:px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-lg transition-all text-xs md:text-sm font-semibold">
                    <i class="fas fa-plus mr-1 md:mr-2"></i>Apply for Loan
                </a>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Loans data...</span>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-xl shadow-md">
        <div class="flex items-center gap-2 text-sm font-medium">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-purple-100 text-[10px] font-medium mb-0.5">Total Loans</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['total']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-green-100 text-[10px] font-medium mb-0.5">Active</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['active']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-yellow-100 text-[10px] font-medium mb-0.5">Pending</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['pending']) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg">
            <p class="text-blue-100 text-[10px] font-medium mb-0.5">Completed</p>
            <h3 class="text-xl font-bold">{{ number_format($stats['completed']) }}</h3>
        </div>
    </div>

    <!-- Advanced Search and Filters -->
    <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('shareholder.loans') }}" x-data="{ showAdvanced: false }">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <!-- Basic Search Section -->
                <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search loans..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="repaid" {{ request('status') == 'repaid' ? 'selected' : '' }}>Repaid</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount (High-Low)</option>
                                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('shareholder.loans') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters Section -->
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-purple-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div class="flex gap-1">
                                <input type="number" name="amount_min" value="{{ request('amount_min') }}" placeholder="Min Amount" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                                <input type="number" name="amount_max" value="{{ request('amount_max') }}" placeholder="Max" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            </div>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
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
                <thead class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600">
                    <tr class="border-b-2 border-white/20">
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Loan Details</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Interest</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-white/20">Progress</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($loans as $loan)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-purple-50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 border-l-4 border-purple-500">
                        <td class="px-6 py-4 whitespace-nowrap relative border-r border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center ring-2 ring-purple-500 ring-offset-2">
                                    <i class="fas fa-money-bill-wave text-white text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Loan #{{ $loan->id }}</p>
                                    <p class="text-xs text-gray-500">{{ $loan->purpose ?? 'General Purpose' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-gray-900">UGX {{ number_format($loan->amount) }}</p>
                                <p class="text-xs text-gray-500">Paid: UGX {{ number_format($loan->paid_amount ?? 0) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <span class="text-sm font-semibold text-gray-900">{{ $loan->interest_rate ?? 0 }}%</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'approved' => 'bg-green-100 text-green-800 border-green-200',
                                    'repaid' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $statusKey = ($loan->status === 'approved' && $loan->remaining_balance <= 0) ? 'repaid' : $loan->status;
                                $colorClass = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full border {{ $colorClass }}">
                                {{ $loan->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            @php
                                $progress = $loan->amount > 0 ? (($loan->paid_amount ?? 0) / $loan->amount) * 100 : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($progress, 1) }}% paid</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('shareholder.loans.show', $loan->id) }}" class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition-all duration-200 group inline-block" title="View Loan">
                                <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-money-bill-wave text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No loans found</p>
                                <p class="text-gray-400 text-sm mt-2">You haven't taken any loans yet</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $loans->links() }}
        </div>
    </div>

    <div id="loan-applications" class="mt-6 bg-white rounded-2xl shadow-xl overflow-hidden border {{ request('tab') === 'applications' ? 'border-indigo-300 ring-2 ring-indigo-200' : 'border-gray-100' }}">
        <div class="px-4 md:px-6 py-4 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border-b border-indigo-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-lg md:text-xl font-bold text-gray-900">My Loan Applications</h2>
                    <p class="text-xs md:text-sm text-gray-600">All applications are now managed on this page</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div class="bg-indigo-100 text-indigo-900 rounded-lg px-3 py-2 text-center">
                        <p class="text-[10px] uppercase font-semibold">Total</p>
                        <p class="text-sm md:text-base font-bold">{{ number_format($applicationStats['total'] ?? 0) }}</p>
                    </div>
                    <div class="bg-yellow-100 text-yellow-900 rounded-lg px-3 py-2 text-center">
                        <p class="text-[10px] uppercase font-semibold">Pending</p>
                        <p class="text-sm md:text-base font-bold">{{ number_format($applicationStats['pending'] ?? 0) }}</p>
                    </div>
                    <div class="bg-green-100 text-green-900 rounded-lg px-3 py-2 text-center">
                        <p class="text-[10px] uppercase font-semibold">Approved</p>
                        <p class="text-sm md:text-base font-bold">{{ number_format($applicationStats['approved'] ?? 0) }}</p>
                    </div>
                    <div class="bg-red-100 text-red-900 rounded-lg px-3 py-2 text-center">
                        <p class="text-[10px] uppercase font-semibold">Rejected</p>
                        <p class="text-sm md:text-base font-bold">{{ number_format($applicationStats['rejected'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3 md:p-4 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('shareholder.loans') }}" class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <input type="hidden" name="tab" value="applications">
                <div class="md:col-span-4">
                    <input type="text" name="app_search" value="{{ request('app_search') }}" placeholder="Search applications..." class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                </div>
                <div class="md:col-span-2">
                    <select name="app_status" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('app_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('app_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('app_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <select name="app_sort" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                        <option value="">Sort By</option>
                        <option value="newest" {{ request('app_sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ request('app_sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="amount_high" {{ request('app_sort') === 'amount_high' ? 'selected' : '' }}>Amount High-Low</option>
                        <option value="amount_low" {{ request('app_sort') === 'amount_low' ? 'selected' : '' }}>Amount Low-High</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <select name="applications_per_page" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                        <option value="5" {{ request('applications_per_page') == '5' ? 'selected' : '' }}>5 per page</option>
                        <option value="10" {{ request('applications_per_page', '10') == '10' ? 'selected' : '' }}>10 per page</option>
                        <option value="15" {{ request('applications_per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                        <option value="20" {{ request('applications_per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                        <option value="50" {{ request('applications_per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-xs bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg font-semibold">
                        <i class="fas fa-search mr-1"></i>Search
                    </button>
                    <a href="{{ route('shareholder.loans', ['tab' => 'applications']) }}#loan-applications" class="px-3 py-2 text-xs bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="app_date_from" value="{{ request('app_date_from') }}" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white" />
                </div>
                <div class="md:col-span-2">
                    <input type="date" name="app_date_to" value="{{ request('app_date_to') }}" class="w-full px-3 py-2 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white" />
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-white uppercase tracking-wider">Application</th>
                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-white uppercase tracking-wider">Amount</th>
                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-white uppercase tracking-wider hidden lg:table-cell">Purpose</th>
                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-white uppercase tracking-wider">Status</th>
                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-white uppercase tracking-wider hidden md:table-cell">Applied</th>
                        <th class="px-4 md:px-6 py-3 text-center text-[10px] md:text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($applications ?? [] as $application)
                    <tr class="transition-all duration-200 {{ $loop->iteration % 2 == 0 ? 'bg-indigo-50/50' : 'bg-white' }} hover:bg-gradient-to-r hover:from-indigo-50 hover:to-pink-50">
                        <td class="px-4 md:px-6 py-4">
                            <p class="text-xs md:text-sm font-semibold text-gray-900">{{ $application->application_id ?? ('#' . $application->id) }}</p>
                            <p class="text-[11px] text-gray-500 lg:hidden">{{ \Illuminate\Support\Str::limit($application->purpose ?? 'N/A', 28) }}</p>
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            <p class="text-xs md:text-sm font-bold text-gray-900">UGX {{ number_format($application->amount ?? 0) }}</p>
                            <p class="text-[11px] text-gray-500 hidden md:block">{{ $application->repayment_months ?? 'N/A' }} months</p>
                        </td>
                        <td class="px-4 md:px-6 py-4 text-xs md:text-sm text-gray-700 hidden lg:table-cell">
                            {{ \Illuminate\Support\Str::limit($application->purpose ?? 'N/A', 80) }}
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            @php
                                $applicationStatus = strtolower((string) ($application->status ?? 'pending'));
                                $applicationStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'approved' => 'bg-green-100 text-green-800 border-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 text-[10px] md:text-xs font-semibold rounded-full border {{ $applicationStatusColors[$applicationStatus] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                {{ ucfirst($applicationStatus) }}
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-4 text-xs md:text-sm text-gray-600 hidden md:table-cell">
                            {{ optional($application->created_at)->format('M d, Y') ?? 'N/A' }}
                        </td>
                        <td class="px-4 md:px-6 py-4 text-center">
                            <a href="{{ route('shareholder.loans.applications.show', $application->id) }}" class="p-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-all duration-200 inline-block" title="View Application">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-invoice text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-600 text-base font-semibold">No loan applications found</p>
                                <p class="text-gray-400 text-xs mt-1">Submit a loan application to see it here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $applications->links() }}
        </div>
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
    background: linear-gradient(to right, transparent, #a855f7, #ec4899, transparent);
    opacity: 0.3;
}
tr:hover::after { opacity: 0.6; }
</style>
@endsection
