@extends('layouts.cashier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-emerald-600 to-teal-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-chart-bar text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 bg-clip-text text-transparent mb-1 md:mb-2">System Reports</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Generate and analyze comprehensive reports</p>
            </div>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-emerald-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Reports data...</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Income</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($summary['total_income'] ?? 0, 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-arrow-down text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Expenses</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($summary['total_expenses'] ?? 0, 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-arrow-up text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[8px] md:text-[10px] font-medium mb-0.5">Net Balance</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($summary['net_balance'] ?? 0, 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-balance-scale text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-[8px] md:text-[10px] font-medium mb-0.5">Transactions</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($summary['total_transactions'] ?? 0) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-exchange-alt text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-yellow-600 to-amber-600 p-4 text-center">
                <i class="fas fa-users text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Members</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('cashier.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="members">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-yellow-600 to-amber-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-4 text-center">
                <i class="fas fa-chart-line text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Financial</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('cashier.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="financial">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-orange-600 to-red-600 p-4 text-center">
                <i class="fas fa-hand-holding-usd text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Loans</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('cashier.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="loans">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-cyan-600 to-teal-600 p-4 text-center">
                <i class="fas fa-exchange-alt text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Transactions</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('cashier.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="transactions">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-cyan-600 to-teal-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 p-4 text-center">
                <i class="fas fa-arrow-down text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Deposits</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('cashier.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="deposits">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 p-4 text-center">
                <i class="fas fa-arrow-up text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Withdrawals</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('cashier.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="withdrawals">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
            <h3 class="text-white text-lg font-bold flex items-center gap-2">
                <i class="fas fa-file-alt"></i>
                Generate Custom Report
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('cashier.reports.generate') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-file-alt text-emerald-600"></i>
                            Report Type *
                        </label>
                        <select name="type" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-sm appearance-none bg-white">
                            <option value="">Select Type</option>
                            <option value="members">Members Report</option>
                            <option value="financial">Financial Report</option>
                            <option value="loans">Loans Report</option>
                            <option value="transactions">Transactions Report</option>
                            <option value="deposits">Deposits Report</option>
                            <option value="withdrawals">Withdrawals Report</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-calendar-plus text-teal-600"></i>
                            From Date *
                        </label>
                        <input type="date" name="from_date" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-calendar-check text-cyan-600"></i>
                            To Date *
                        </label>
                        <input type="date" name="to_date" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-file-download text-blue-600"></i>
                            Format *
                        </label>
                        <select name="format" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm appearance-none bg-white">
                            <option value="html">HTML/PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Generated Reports History Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
            <h3 class="text-white text-lg font-bold flex items-center gap-2">
                <i class="fas fa-file-invoice"></i>
                Generated Reports History
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Report Name</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Format</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Generated</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports ?? [] as $report)
                    <tr class="hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 transition-all duration-200">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-pdf text-red-600"></i>
                                <span class="font-semibold text-gray-900">{{ $report->name ?? ucfirst($report->type) . ' Report' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                {{ ucfirst($report->type ?? 'Summary') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $report->from_date ?? 'N/A' }} - {{ $report->to_date ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                {{ strtoupper($report->format ?? 'HTML') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-teal-500 mr-1"></i>{{ $report->created_at ? $report->created_at->format('M d, Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('cashier.reports.view', $report->id) }}" class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-all transform hover:scale-110" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('cashier.reports.delete', $report->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this report?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all transform hover:scale-110" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-emerald-100 to-teal-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-file-alt text-emerald-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No reports generated yet</p>
                                <p class="text-gray-400 text-sm mt-2">Generate your first report using the form above</p>
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
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
@keyframes slide-text { 0% { left: 0%; opacity: 1; } 95% { opacity: 1; } 100% { left: 100%; opacity: 0; } }
.animate-slide-text { animation: slide-text 5s ease-out forwards; }
</style>
@endsection
