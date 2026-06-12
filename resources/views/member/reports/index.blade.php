@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-chart-bar text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 bg-clip-text text-transparent mb-1 md:mb-2">Financial Reports</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Generate and analyze comprehensive financial reports</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Reports data...</span>
    </div>

    <!-- Summary Stats -->
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

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-chart-line"></i>
                    Income vs Expenses
                </h3>
            </div>
            <div class="p-6">
                <canvas id="incomeExpenseChart" height="200"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-pink-600 to-purple-600 px-6 py-4">
                <h3 class="text-white text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-chart-pie"></i>
                    Transaction Distribution
                </h3>
            </div>
            <div class="p-6">
                <canvas id="transactionChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 text-center">
                <i class="fas fa-briefcase text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Portfolio</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('shareholder.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="portfolio">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-4 text-center">
                <i class="fas fa-coins text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Dividends</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('shareholder.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="dividends">
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
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-center">
                <i class="fas fa-chart-line text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Performance</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('shareholder.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="performance">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-orange-600 to-red-600 p-4 text-center">
                <i class="fas fa-file-invoice-dollar text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Tax</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('shareholder.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="tax">
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
            <div class="bg-gradient-to-r from-pink-600 to-pink-700 p-4 text-center">
                <i class="fas fa-exchange-alt text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Transactions</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('shareholder.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="transactions">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-pink-600 to-pink-700 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transform hover:scale-105 transition-all">
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 p-4 text-center">
                <i class="fas fa-piggy-bank text-white text-2xl mb-2"></i>
                <h3 class="text-white text-sm font-bold">Savings</h3>
            </div>
            <div class="p-3">
                <form action="{{ route('shareholder.reports.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="savings">
                    <input type="hidden" name="from_date" value="{{ date('Y-m-01') }}">
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="format" value="html">
                    <button type="submit" class="w-full px-3 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-lg hover:shadow-lg transition-all text-xs font-bold">
                        <i class="fas fa-download mr-1"></i>Generate
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Report Generator -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h3 class="text-white text-lg font-bold flex items-center gap-2">
                <i class="fas fa-file-alt"></i>
                Generate Custom Report
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('shareholder.reports.generate') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-file-alt text-purple-600"></i>
                            Report Type *
                        </label>
                        <select name="type" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm appearance-none bg-white">
                            <option value="">Select Type</option>
                            <option value="portfolio">Portfolio Report</option>
                            <option value="dividends">Dividends Report</option>
                            <option value="performance">Performance Report</option>
                            <option value="tax">Tax Report</option>
                            <option value="transactions">Transactions Report</option>
                            <option value="savings">Savings Report</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-calendar-plus text-pink-600"></i>
                            From Date *
                        </label>
                        <input type="date" name="from_date" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-calendar-check text-purple-600"></i>
                            To Date *
                        </label>
                        <input type="date" name="to_date" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <i class="fas fa-file-download text-pink-600"></i>
                            Format *
                        </label>
                        <select name="format" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm appearance-none bg-white">
                            <option value="html">HTML/PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
                            <i class="fas fa-download mr-2"></i>Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Generated Reports Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6">
        <div class="bg-gradient-to-r from-pink-600 to-purple-600 px-6 py-4">
            <h3 class="text-white text-lg font-bold flex items-center gap-2">
                <i class="fas fa-file-invoice"></i>
                Generated Reports History
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-purple-50 via-pink-50 to-purple-50">
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
                    <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-all duration-200">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-pdf text-red-600"></i>
                                <span class="font-semibold text-gray-900">{{ $report->name ?? 'Financial Report' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                {{ ucfirst($report->type ?? 'Summary') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $report->from_date ?? 'N/A' }} - {{ $report->to_date ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                {{ strtoupper($report->format ?? 'PDF') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar text-purple-500 mr-1"></i>{{ $report->created_at ? $report->created_at->format('M d, Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('shareholder.reports.view', $report->id) }}" class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-all transform hover:scale-110" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="printReport({{ $report->id }})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-all transform hover:scale-110" title="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-purple-100 to-pink-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-file-alt text-purple-400 text-5xl"></i>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function printReport(reportId) {
    const printWindow = window.open(`/shareholder/reports/view/${reportId}`, '_blank');
    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close();
        };
    };
}

// Income vs Expenses Chart
const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
new Chart(incomeExpenseCtx, {
    type: 'bar',
    data: {
        labels: ['Income', 'Expenses'],
        datasets: [{
            label: 'Amount (UGX)',
            data: [{{ $summary['total_income'] ?? 0 }}, {{ $summary['total_expenses'] ?? 0 }}],
            backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
                'rgb(16, 185, 129)',
                'rgb(239, 68, 68)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Transaction Distribution Chart
const transactionCtx = document.getElementById('transactionChart').getContext('2d');
new Chart(transactionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Income', 'Expenses', 'Balance'],
        datasets: [{
            data: [
                {{ $summary['total_income'] ?? 0 }},
                {{ $summary['total_expenses'] ?? 0 }},
                {{ $summary['net_balance'] ?? 0 }}
            ],
            backgroundColor: [
                'rgb(16, 185, 129)',
                'rgb(239, 68, 68)',
                'rgb(59, 130, 246)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection

