<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Loan Applications</h2>
        <p class="text-gray-600">Review and process pending loan applications</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.loans.approvals') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-check-circle mr-2"></i>Approved Loans
        </a>
        <a href="{{ route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.loans.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-list mr-2"></i>All Loans
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Pending Applications</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $applications->total() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Total Amount Requested</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($applications->sum('amount'), 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-600">Avg. Loan Amount</p>
        <p class="text-2xl font-bold text-purple-600">{{ $applications->count() > 0 ? number_format($applications->avg('amount'), 2) : '0.00' }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    @include('shared.admin-cashier.loan-applications.partials.table')
</div>