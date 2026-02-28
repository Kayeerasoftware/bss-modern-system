@extends('layouts.admin')

@section('content')
@php
    $logsPayload = $logsData ?? [];
    $userSuggestions = collect($logsPayload)->pluck('user')->filter()->unique()->sort()->values();
    $actionSuggestions = collect($logsPayload)->pluck('action')->filter()->map(fn ($v) => strtolower((string) $v))->unique()->sort()->values();
    $searchSuggestions = collect($logsPayload)
        ->flatMap(fn ($log) => [
            (string) ($log['user'] ?? ''),
            (string) ($log['action'] ?? ''),
            (string) ($log['module'] ?? ''),
            (string) ($log['details'] ?? ''),
        ])
        ->filter()
        ->map(fn ($v) => mb_substr(trim((string) $v), 0, 90))
        ->unique()
        ->take(120)
        ->values();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-zinc-50 to-stone-100 p-3 md:p-6" x-data="auditLogManager({{ \Illuminate\Support\Js::from($logsPayload) }})" x-init="init()">
    <div class="max-w-7xl mx-auto space-y-5">
        <div class="rounded-3xl border border-slate-200 bg-white/90 shadow-sm backdrop-blur">
            <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 md:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-red-500 text-white shadow-lg">
                        <i class="fas fa-clipboard-list text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-slate-800 md:text-2xl">Audit Log Center</h1>
                        <p class="text-xs text-slate-500 md:text-sm">Beautiful, searchable, sortable system activity records</p>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-xs font-semibold text-emerald-700">
                    <i class="fas fa-shield-check"></i>
                    <span x-text="filteredLogs.length + ' visible of ' + logs.length"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Logs</p>
                <p class="mt-1 text-2xl font-extrabold text-slate-800" x-text="stats.total"></p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Today</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-800" x-text="stats.today"></p>
            </div>
            <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">This Week</p>
                <p class="mt-1 text-2xl font-extrabold text-blue-800" x-text="stats.week"></p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Critical / Failed</p>
                <p class="mt-1 text-2xl font-extrabold text-rose-800" x-text="stats.critical"></p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <div class="md:col-span-4">
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Smart Search</label>
                    <div class="relative">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input
                            x-model="filters.search"
                            list="auditSearchSuggestions"
                            type="text"
                            placeholder="Type user, action, module, details..."
                            class="w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                        >
                    </div>
                    <datalist id="auditSearchSuggestions">
                        @foreach($searchSuggestions as $item)
                            <option value="{{ $item }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Action</label>
                    <select x-model="filters.action" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                        <option value="">All Actions</option>
                        @foreach($actionSuggestions as $action)
                            <option value="{{ strtolower((string) $action) }}">{{ ucfirst((string) $action) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">User</label>
                    <input
                        x-model="filters.user"
                        list="auditUserSuggestions"
                        type="text"
                        placeholder="User name or email"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                    >
                    <datalist id="auditUserSuggestions">
                        @foreach($userSuggestions as $userName)
                            <option value="{{ $userName }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Status</label>
                    <select x-model="filters.status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                        <option value="">All Status</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Sort</label>
                    <select x-model="filters.sort" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="action_asc">Action A-Z</option>
                        <option value="user_asc">User A-Z</option>
                        <option value="module_asc">Module A-Z</option>
                    </select>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                        Active Filters: <span x-text="activeFilterCount"></span>
                    </span>
                    <template x-if="filters.search">
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700" x-text="'Search: ' + filters.search"></span>
                    </template>
                    <template x-if="filters.action">
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700" x-text="'Action: ' + filters.action"></span>
                    </template>
                    <template x-if="filters.user">
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700" x-text="'User: ' + filters.user"></span>
                    </template>
                    <template x-if="filters.status">
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700" x-text="'Status: ' + filters.status"></span>
                    </template>
                </div>
                <button @click="clearFilters()" class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                    <i class="fas fa-rotate-left mr-1"></i>Clear Filters
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-600">
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Module</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(log, index) in filteredLogs" :key="log.id + '-' + index">
                            <tr class="transition hover:bg-amber-50/30">
                                <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-slate-700" x-text="log.timestamp"></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <img :src="log.userPhoto" alt="" class="h-8 w-8 rounded-full border border-slate-200 object-cover">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800" x-text="log.user"></p>
                                            <p class="text-xs text-slate-500" x-text="log.userRole"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="log.actionBadge" x-text="log.action"></span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-slate-700" x-text="log.module"></td>
                                <td class="max-w-md px-4 py-3 text-xs text-slate-600">
                                    <p class="truncate" x-text="log.description || log.details"></p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="log.statusClass || 'bg-slate-100 text-slate-700'" x-text="statusText(log)"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="openDetails(log)" class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">
                                        <i class="fas fa-eye mr-1"></i>Details
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="filteredLogs.length === 0" class="px-6 py-16 text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <i class="fas fa-inbox text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-600">No matching logs on this page</p>
                <p class="mt-1 text-xs text-slate-500">Try changing search terms or clearing filters</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-600 shadow-sm">
            <p>Server page: {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }}</p>
            <div>{{ $logs->links() }}</div>
        </div>
    </div>

    <div x-show="showDetailsModal" x-transition.opacity @click.self="showDetailsModal = false" class="fixed inset-0 z-50 bg-slate-900/60 p-4" style="display: none;">
        <div class="mx-auto max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-3xl bg-white shadow-2xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Audit Activity Details</h3>
                    <p class="text-xs text-slate-500" x-text="'Log ID: ' + (selectedLog?.id || 'N/A')"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="exportToPDF()" class="rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-700">
                        <i class="fas fa-file-pdf mr-1"></i>Export
                    </button>
                    <button @click="showDetailsModal = false" class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                        <i class="fas fa-xmark mr-1"></i>Close
                    </button>
                </div>
            </div>

            <div class="space-y-4 p-5">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <img :src="selectedLog?.userPhoto || ''" alt="" class="h-14 w-14 rounded-full border border-slate-200 object-cover">
                        <div class="flex-1">
                            <p class="text-base font-bold text-slate-800" x-text="selectedLog?.user || 'N/A'"></p>
                            <p class="text-xs text-slate-500" x-text="selectedLog?.userRole || 'System User'"></p>
                            <p class="text-xs text-slate-500" x-text="(selectedLog?.userEmail || 'N/A') + ' | ' + (selectedLog?.userPhone || 'N/A')"></p>
                        </div>
                        <div class="text-right">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="selectedLog?.actionBadge || 'bg-slate-200 text-slate-700'" x-text="selectedLog?.action || 'Action'"></span>
                            <p class="mt-2 text-xs text-slate-500" x-text="selectedLog?.timestamp || ''"></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <div class="rounded-xl border border-slate-200 p-3">
                        <p class="text-[11px] font-bold uppercase text-slate-500">Module</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800" x-text="selectedLog?.module || 'N/A'"></p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3">
                        <p class="text-[11px] font-bold uppercase text-slate-500">IP</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800" x-text="selectedLog?.ip || 'N/A'"></p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3">
                        <p class="text-[11px] font-bold uppercase text-slate-500">Browser</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800" x-text="selectedLog?.browser || 'N/A'"></p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3">
                        <p class="text-[11px] font-bold uppercase text-slate-500">Status</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800" x-text="statusText(selectedLog || {})"></p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="mb-2 text-[11px] font-bold uppercase text-slate-500">What Happened</p>
                    <p class="text-sm text-slate-700" x-text="selectedLog?.description || selectedLog?.details || 'No description available.'"></p>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="mb-2 text-[11px] font-bold uppercase text-slate-500">Technical Context</p>
                    <div class="grid grid-cols-1 gap-2 text-xs text-slate-700 md:grid-cols-2">
                        <p><span class="font-semibold text-slate-500">User Agent:</span> <span x-text="selectedLog?.userAgent || 'N/A'"></span></p>
                        <p><span class="font-semibold text-slate-500">Platform:</span> <span x-text="selectedLog?.platform || 'N/A'"></span></p>
                        <p><span class="font-semibold text-slate-500">Device:</span> <span x-text="selectedLog?.device || 'N/A'"></span></p>
                        <p><span class="font-semibold text-slate-500">Location:</span> <span x-text="selectedLog?.location || 'N/A'"></span></p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase text-slate-500">Field Changes</p>
                        <span class="text-[11px] text-slate-400" x-text="'Fields: ' + changeRows(selectedLog).length"></span>
                    </div>

                    <template x-if="changeRows(selectedLog).length > 0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-slate-50 text-slate-600">
                                    <tr>
                                        <th class="px-2 py-2 text-left">Field</th>
                                        <th class="px-2 py-2 text-left">Before</th>
                                        <th class="px-2 py-2 text-left">After</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="row in changeRows(selectedLog)" :key="row.field">
                                        <tr>
                                            <td class="px-2 py-2 font-semibold text-slate-700" x-text="row.field"></td>
                                            <td class="px-2 py-2 text-slate-600 break-all" x-text="formatValue(row.before)"></td>
                                            <td class="px-2 py-2 text-slate-700 break-all" x-text="formatValue(row.after)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <template x-if="changeRows(selectedLog).length === 0">
                        <p class="text-xs text-slate-500">No structured before/after changes available for this log.</p>
                    </template>

                    <div class="mt-3 rounded-lg bg-slate-900 p-3">
                        <pre class="whitespace-pre-wrap text-[11px] text-emerald-300" x-text="JSON.stringify((selectedLog && selectedLog.changes) || {}, null, 2)"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function auditLogManager(initialLogs) {
    return {
        logs: Array.isArray(initialLogs) ? initialLogs : [],
        showDetailsModal: false,
        selectedLog: null,
        filters: {
            search: @js((string) request('search', '')),
            action: @js(strtolower((string) request('action', ''))),
            user: @js((string) request('user', '')),
            status: '',
            sort: @js((string) request('sort', 'newest') ?: 'newest'),
        },
        stats: { total: 0, today: 0, week: 0, critical: 0 },

        init() {
            this.rebuildStats();
        },

        get activeFilterCount() {
            let count = 0;
            if (this.filters.search) count++;
            if (this.filters.action) count++;
            if (this.filters.user) count++;
            if (this.filters.status) count++;
            return count;
        },

        get filteredLogs() {
            const search = (this.filters.search || '').toLowerCase().trim();
            const action = (this.filters.action || '').toLowerCase().trim();
            const user = (this.filters.user || '').toLowerCase().trim();
            const status = (this.filters.status || '').toLowerCase().trim();

            let rows = this.logs.filter((log) => {
                const actionValue = String(log.action || '').toLowerCase();
                const userValue = String(log.user || '').toLowerCase();
                const statusValue = String(log.status || '').toLowerCase();
                const statusCode = Number(log.statusCode || 0);
                const haystack = [
                    log.timestamp,
                    log.user,
                    log.action,
                    log.module,
                    log.details,
                    log.description,
                    log.ip,
                    log.userEmail,
                ].join(' ').toLowerCase();

                if (search && !haystack.includes(search)) return false;
                if (action && actionValue !== action) return false;
                if (user && !userValue.includes(user)) return false;

                if (status) {
                    if (status === 'success' && !(statusCode >= 200 && statusCode < 300 || statusValue.includes('success'))) return false;
                    if (status === 'warning' && !(statusCode >= 300 && statusCode < 400 || statusValue.includes('redirect'))) return false;
                    if (status === 'failed' && !(statusCode >= 400 || statusValue.includes('fail'))) return false;
                }

                return true;
            });

            const sort = this.filters.sort || 'newest';
            rows.sort((a, b) => {
                if (sort === 'oldest') return this.ts(a) - this.ts(b);
                if (sort === 'action_asc') return String(a.action || '').localeCompare(String(b.action || ''));
                if (sort === 'user_asc') return String(a.user || '').localeCompare(String(b.user || ''));
                if (sort === 'module_asc') return String(a.module || '').localeCompare(String(b.module || ''));
                return this.ts(b) - this.ts(a);
            });

            return rows;
        },

        ts(log) {
            const raw = String(log.timestamp || '').replace(' ', 'T');
            const time = Date.parse(raw);
            return Number.isNaN(time) ? 0 : time;
        },

        statusText(log) {
            if (!log) return 'Unknown';
            if (log.statusCode) return `${log.status || 'Unknown'} (${log.statusCode})`;
            return log.status || 'Unknown';
        },

        clearFilters() {
            this.filters.search = '';
            this.filters.action = '';
            this.filters.user = '';
            this.filters.status = '';
            this.filters.sort = 'newest';
        },

        openDetails(log) {
            this.selectedLog = log;
            this.showDetailsModal = true;
        },

        changeRows(log) {
            if (!log || !log.changes || typeof log.changes !== 'object') return [];
            const before = (log.changes.before && typeof log.changes.before === 'object') ? log.changes.before : {};
            const after = (log.changes.after && typeof log.changes.after === 'object') ? log.changes.after : {};
            const fields = Array.from(new Set([...Object.keys(before), ...Object.keys(after)]));
            return fields.map((field) => ({
                field,
                before: before[field] ?? null,
                after: after[field] ?? null,
            }));
        },

        formatValue(value) {
            if (value === null || value === undefined || value === '') return 'null';
            if (typeof value === 'object') return JSON.stringify(value);
            return String(value);
        },

        rebuildStats() {
            const now = new Date();
            const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
            const day = now.getDay() || 7;
            const weekStartDate = new Date(now);
            weekStartDate.setDate(now.getDate() - day + 1);
            weekStartDate.setHours(0, 0, 0, 0);
            const startOfWeek = weekStartDate.getTime();

            this.stats.total = this.logs.length;
            this.stats.today = this.logs.filter((log) => this.ts(log) >= startOfToday).length;
            this.stats.week = this.logs.filter((log) => this.ts(log) >= startOfWeek).length;
            this.stats.critical = this.logs.filter((log) => {
                const code = Number(log.statusCode || 0);
                const status = String(log.status || '').toLowerCase();
                return code >= 400 || status.includes('fail');
            }).length;
        },

        exportToPDF() {
            if (!this.selectedLog) return;
            const log = this.selectedLog;
            const html = `
                <html>
                <head>
                    <title>Audit Log ${log.id || ''}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; color: #1f2937; }
                        h1 { color: #c2410c; margin-bottom: 10px; }
                        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
                        .label { color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; }
                        .value { margin-top: 4px; font-size: 14px; }
                        pre { background: #111827; color: #86efac; padding: 10px; border-radius: 6px; overflow: auto; font-size: 11px; }
                    </style>
                </head>
                <body>
                    <h1>Audit Log Details</h1>
                    <div class="box"><div class="label">Log ID</div><div class="value">${log.id || 'N/A'}</div></div>
                    <div class="box"><div class="label">User</div><div class="value">${log.user || 'N/A'} (${log.userRole || 'N/A'})</div></div>
                    <div class="box"><div class="label">Action</div><div class="value">${log.action || 'N/A'}</div></div>
                    <div class="box"><div class="label">Timestamp</div><div class="value">${log.timestamp || 'N/A'}</div></div>
                    <div class="box"><div class="label">Description</div><div class="value">${(log.description || log.details || '').replace(/</g, '&lt;')}</div></div>
                    <div class="box"><div class="label">Raw Changes</div><pre>${JSON.stringify(log.changes || {}, null, 2)}</pre></div>
                </body>
                </html>
            `;

            const w = window.open('', '', 'width=900,height=700');
            w.document.write(html);
            w.document.close();
            w.focus();
            setTimeout(() => {
                w.print();
                w.close();
            }, 250);
        },
    };
}
</script>
@endsection
