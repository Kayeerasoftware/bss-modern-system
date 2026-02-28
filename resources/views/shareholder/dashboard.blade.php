@extends('layouts.shareholder')

@section('title', 'Shareholder Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50 p-3 md:p-6">
    <div class="mb-5 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-cyan-900 p-4 md:p-6 text-white shadow-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="h-14 w-14 overflow-hidden rounded-xl border border-white/20 bg-white/10">
                    <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
                </div>
                <div>
                    <h1 class="text-xl font-bold md:text-3xl">Shareholder Portfolio Dashboard</h1>
                    <p class="mt-1 text-xs text-cyan-100 md:text-sm">
                        Real-time performance, dividends, cash flow, share accumulation, and project signals for {{ $member->full_name }}.
                    </p>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-cyan-100">
                        <span class="rounded-full bg-white/10 px-3 py-1">Member ID: {{ $member->member_id }}</span>
                        <span class="rounded-full bg-white/10 px-3 py-1">Net Worth: UGX {{ number_format($stats['netWorth'] ?? 0, 0) }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label for="yearFilter" class="text-xs font-semibold uppercase tracking-wide text-cyan-200">Analytics Period</label>
                <select id="yearFilter" class="rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-sm font-semibold text-white outline-none ring-0">
                    @for ($year = now()->year; $year >= 2023; $year--)
                        <option value="{{ $year }}" {{ $selectedYear === (string) $year ? 'selected' : '' }} class="text-slate-800">
                            {{ $year }}
                        </option>
                    @endfor
                    <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }} class="text-slate-800">All Years</option>
                </select>
                <a href="{{ route('shareholder.financial') }}" class="rounded-lg bg-cyan-500 px-4 py-2 text-center text-xs font-bold text-slate-900 transition hover:bg-cyan-400">
                    Financial Report
                </a>
                <a href="{{ route('shareholder.dividends.index') }}" class="rounded-lg bg-white/10 px-4 py-2 text-center text-xs font-bold transition hover:bg-white/20">
                    Dividends
                </a>
            </div>
        </div>
    </div>

    @include('shareholder.partials.stats-cards')
    @include('shareholder.partials.charts')

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 md:text-base">Recent Dividend Activity</h3>
                <a href="{{ route('shareholder.dividends.index') }}" class="text-xs font-semibold text-cyan-700 hover:underline">Open Dividends</a>
            </div>
            <div id="recentDividendsWrap" class="space-y-2"></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 md:text-base">Recent Share Purchases</h3>
                <a href="{{ route('shareholder.investments.index') }}" class="text-xs font-semibold text-cyan-700 hover:underline">Open Investments</a>
            </div>
            <div id="recentSharesWrap" class="space-y-2"></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 md:text-base">Recent Transactions</h3>
                <a href="{{ route('shareholder.transactions') }}" class="text-xs font-semibold text-cyan-700 hover:underline">Open Transactions</a>
            </div>
            <div id="recentTransactionsWrap" class="space-y-2"></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 md:text-base">Active Opportunities & Project Watch</h3>
                <a href="{{ route('shareholder.projects.index') }}" class="text-xs font-semibold text-cyan-700 hover:underline">Open Projects</a>
            </div>
            <div id="opportunitiesWrap" class="space-y-2"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('shareholder.partials.scripts')
@endpush
