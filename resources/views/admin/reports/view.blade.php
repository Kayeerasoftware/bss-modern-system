@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 bg-clip-text text-transparent">
                    {{ ucfirst($type) }} Report
                </h1>
                <p class="text-gray-600 text-sm">{{ $from_date }} to {{ $to_date }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ auth()->user()->role === 'cashier' ? route('cashier.reports.index') : route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>

        <!-- Report Content -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if($type === 'members')
                @include('admin.reports.types.members', ['data' => $data])
            @elseif($type === 'financial')
                @include('admin.reports.types.financial', ['data' => $data])
            @elseif($type === 'loans')
                @include('admin.reports.types.loans', ['data' => $data])
            @elseif($type === 'transactions')
                @include('admin.reports.types.transactions', ['data' => $data])
            @elseif($type === 'deposits')
                @include('admin.reports.types.transactions', ['data' => $data])
            @elseif($type === 'withdrawals')
                @include('admin.reports.types.transactions', ['data' => $data])
            @elseif($type === 'projects')
                @include('admin.reports.types.projects', ['data' => $data])
            @elseif($type === 'audit')
                @include('admin.reports.types.audit', ['data' => $data])
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
}
</style>
@endsection
