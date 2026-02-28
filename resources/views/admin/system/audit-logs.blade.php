@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50 p-3 md:p-6" x-data="auditLogManager()">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-orange-600 to-red-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-history text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 bg-clip-text text-transparent mb-1 md:mb-2">Audit Logs</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Track all system activities and changes</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-orange-500 to-red-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-orange-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Audit Logs...</span>
    </div>

    <input type="hidden" id="system_name" value="{{ $settings['system_name'] ?? 'BSS Investment Group' }}">
    <input type="hidden" id="system_email" value="{{ $settings['system_email'] ?? 'info@bss.com' }}">
    <input type="hidden" id="system_phone" value="{{ $settings['system_phone'] ?? '+250 788 000 000' }}">
    <input type="hidden" id="website" value="{{ $settings['website'] ?? 'www.bss.com' }}">
    <input type="hidden" id="address" value="{{ $settings['address'] ?? 'Kigali, Rwanda' }}">

    <div class="max-w-7xl mx-auto">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-medium">Total Activities</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="stats.total"></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-medium">Today</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="stats.today"></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-medium">This Week</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="stats.week"></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-week text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 font-medium">Critical Events</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="stats.critical"></p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

    <!-- Advanced Search and Filters -->
    <div class="bg-gradient-to-br from-orange-50 via-red-50 to-orange-50 rounded-2xl shadow-lg border border-orange-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('admin.system.audit-logs') }}" x-data="{ showAdvanced: false }">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <div class="bg-white/80 rounded-xl p-2.5 border border-orange-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-orange-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-white" @input.debounce.500ms="filterLogs()">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-bolt absolute left-2.5 top-1/2 transform -translate-y-1/2 text-red-400 text-xs"></i>
                            <select name="action" class="w-full pl-8 pr-2 py-1.5 text-xs border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all appearance-none bg-white" @change="filterLogs()">
                                <option value="">All Actions</option>
                                <option value="create" @selected(request('action') == 'create')>Create</option>
                                <option value="update" @selected(request('action') == 'update')>Update</option>
                                <option value="delete" @selected(request('action') == 'delete')>Delete</option>
                                <option value="login" @selected(request('action') == 'login')>Login</option>
                                <option value="logout" @selected(request('action') == 'logout')>Logout</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-sort-amount-down absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                            <select name="sort" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="filterLogs()">
                                <option value="">Sort By</option>
                                <option value="newest" @selected(request('sort') == 'newest')>Newest First</option>
                                <option value="oldest" @selected(request('sort') == 'oldest')>Oldest First</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('admin.system.audit-logs') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-orange-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="filterLogs()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="filterLogs()">
                            <input type="text" name="user" value="{{ request('user') }}" placeholder="Filter by user" class="w-full px-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <select name="per_page" class="w-full px-2 py-1.5 text-xs border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent appearance-none bg-white" @change="filterLogs()">
                                <option value="10" @selected(request('per_page') == '10')>10 per page</option>
                                <option value="15" @selected(request('per_page') == '15')>15 per page</option>
                                <option value="20" @selected(request('per_page') == '20')>20 per page</option>
                                <option value="50" @selected(request('per_page') == '50')>50 per page</option>
                                <option value="100" @selected(request('per_page') == '100')>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

        <!-- Logs Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Module</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Details</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">IP Address</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(log, index) in filteredLogs" :key="index">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="log.timestamp"></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" :class="log.userColor" x-text="log.user.charAt(0)"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900" x-text="log.user"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="log.actionBadge" x-text="log.action"></span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700" x-text="log.module"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="log.details"></td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500" x-text="log.ip"></td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <button @click="viewDetails(log)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div x-show="filteredLogs.length === 0" class="text-center py-12">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 font-medium">No audit logs found</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-gray-600">Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs</p>
            <div class="flex gap-2">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div x-show="showDetailsModal" @click.self="showDetailsModal = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-alt text-white text-2xl"></i>
                    <div>
                        <h3 class="text-xl font-bold text-white">Activity Details</h3>
                        <p class="text-orange-100 text-xs" x-text="'Log ID: ' + (selectedLog.id || 'N/A')"></p>
                    </div>
                </div>
                <button @click="showDetailsModal = false" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6">
                <!-- User Info Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-6 border border-blue-200">
                    <div class="flex items-center gap-4">
                        <img :src="selectedLog.userPhoto || '/images/default-avatar.png'" :alt="selectedLog.user" class="w-20 h-20 rounded-full border-4 border-white shadow-lg object-cover">
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-800" x-text="selectedLog.user"></h4>
                            <p class="text-sm text-gray-600" x-text="selectedLog.userRole"></p>
                            <div class="flex gap-3 mt-2 text-xs">
                                <span class="flex items-center gap-1 text-gray-600">
                                    <i class="fas fa-envelope"></i>
                                    <span x-text="selectedLog.userEmail"></span>
                                </span>
                                <span class="flex items-center gap-1 text-gray-600">
                                    <i class="fas fa-phone"></i>
                                    <span x-text="selectedLog.userPhone"></span>
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="selectedLog.actionBadge" x-text="selectedLog.action"></span>
                            <p class="text-xs text-gray-500 mt-2" x-text="selectedLog.timestamp"></p>
                        </div>
                    </div>
                </div>

                <!-- Activity Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-layer-group text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Module</p>
                                <p class="text-sm font-bold text-gray-800" x-text="selectedLog.module"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border-2 border-green-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-network-wired text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">IP Address</p>
                                <p class="text-sm font-bold text-gray-800" x-text="selectedLog.ip"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border-2 border-purple-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Location</p>
                                <p class="text-sm font-bold text-gray-800" x-text="selectedLog.location"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Description -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                        <div>
                            <p class="text-xs font-semibold text-yellow-800 mb-1">Activity Description</p>
                            <p class="text-sm text-gray-700" x-text="selectedLog.description || selectedLog.details"></p>
                        </div>
                    </div>
                </div>

                <!-- Technical Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-2">
                            <i class="fas fa-desktop text-gray-500"></i>User Agent
                        </p>
                        <p class="text-sm text-gray-800" x-text="selectedLog.userAgent"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-2">
                            <i class="fas fa-globe text-gray-500"></i>Browser
                        </p>
                        <p class="text-sm text-gray-800" x-text="selectedLog.browser"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-2">
                            <i class="fas fa-laptop text-gray-500"></i>Device
                        </p>
                        <p class="text-sm text-gray-800" x-text="selectedLog.device"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-2">
                            <i class="fas fa-server text-gray-500"></i>Platform
                        </p>
                        <p class="text-sm text-gray-800" x-text="selectedLog.platform"></p>
                    </div>
                </div>

                <!-- Affected Member (if applicable) -->
                <div x-show="selectedLog.affectedMember" class="bg-white border-2 border-indigo-200 rounded-xl p-4 mb-6">
                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-user-tag text-indigo-600"></i>Affected Member
                    </h4>
                    <div class="flex items-center gap-4">
                        <img :src="selectedLog.affectedMember?.photo || '/images/default-avatar.png'" class="w-16 h-16 rounded-full border-2 border-indigo-300 object-cover">
                        <div class="flex-1 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-600">Name</p>
                                <p class="font-semibold text-gray-800" x-text="selectedLog.affectedMember?.name"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Member ID</p>
                                <p class="font-semibold text-gray-800" x-text="selectedLog.affectedMember?.id"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Email</p>
                                <p class="font-semibold text-gray-800" x-text="selectedLog.affectedMember?.email"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Phone</p>
                                <p class="font-semibold text-gray-800" x-text="selectedLog.affectedMember?.phone"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Changes Made -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-4 mb-6">
                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-gray-600"></i>Changes Made
                    </h4>
                    <div x-show="selectedLog.changeItems && selectedLog.changeItems.length" class="mb-3 overflow-x-auto">
                        <table class="min-w-full text-xs border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">Source</th>
                                    <th class="px-3 py-2 text-left">Field</th>
                                    <th class="px-3 py-2 text-left">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, idx) in selectedLog.changeItems" :key="idx">
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2" x-text="item.source"></td>
                                        <td class="px-3 py-2 font-semibold text-gray-800" x-text="item.field"></td>
                                        <td class="px-3 py-2 text-gray-700 break-all" x-text="item.value === null ? 'null' : item.value"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-green-400 font-mono" x-text="JSON.stringify(selectedLog.changes, null, 2)"></pre>
                    </div>
                </div>

                <!-- Additional Metadata -->
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl p-4">
                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-database text-gray-600"></i>Additional Information
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                        <div>
                            <p class="text-gray-600">Session ID</p>
                            <p class="font-semibold text-gray-800" x-text="selectedLog.sessionId"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Request ID</p>
                            <p class="font-semibold text-gray-800" x-text="selectedLog.requestId"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Duration</p>
                            <p class="font-semibold text-gray-800" x-text="selectedLog.duration"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status</p>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="selectedLog.statusClass || 'bg-gray-100 text-gray-700'" x-text="selectedLog.statusCode ? (selectedLog.status + ' (' + selectedLog.statusCode + ')') : selectedLog.status"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sticky bottom-0 bg-white border-t px-6 py-4 flex justify-between items-center">
                <button @click="exportToPDF()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-file-pdf mr-2"></i>Export as PDF
                </button>
                <button @click="showDetailsModal = false" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function auditLogManager() {
    return {
        showDetailsModal: false,
        selectedLog: {},
        filters: {search: '', action: '', user: '', dateRange: 'all'},
        stats: {total: {{ $logs->count() }}, today: {{ $logs->where('created_at', '>=', now()->startOfDay())->count() }}, week: {{ $logs->where('created_at', '>=', now()->startOfWeek())->count() }}, critical: 0},
        logs: {!! \Illuminate\Support\Js::from($logsData ?? []) !!},
        init() {
            this.stats.critical = this.logs.filter(log => (log.status || '').toLowerCase().includes('fail')).length;
        },
        get filteredLogs() {
            return this.logs.filter(log => {
                const details = (log.details || '').toLowerCase();
                const description = (log.description || '').toLowerCase();
                if (this.filters.search && !details.includes(this.filters.search.toLowerCase()) && !description.includes(this.filters.search.toLowerCase())) return false;
                if (this.filters.action && log.action.toLowerCase() !== this.filters.action.toLowerCase()) return false;
                if (this.filters.user && !log.user.toLowerCase().includes(this.filters.user.toLowerCase())) return false;
                return true;
            });
        },
        filterLogs() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            
            fetch(`{{ route('admin.system.audit-logs') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('table').parentElement;
                document.querySelector('table').parentElement.innerHTML = newTable.innerHTML;
            });
        },
        applyFilters() {
            console.log('Filters applied');
        },
        resetFilters() {
            this.filters = {search: '', action: '', user: '', dateRange: 'all'};
        },
        exportLogs() {
            alert('Exporting audit logs...');
        },
        viewDetails(log) {
            this.selectedLog = log;
            this.showDetailsModal = true;
        },
        exportToPDF() {
            const log = this.selectedLog;
            const orgSettings = {
                name: document.getElementById('system_name').value,
                email: document.getElementById('system_email').value,
                phone: document.getElementById('system_phone').value,
                address: document.getElementById('address').value,
                website: document.getElementById('website').value,
                logo: window.location.origin + '/assets/images/logo.png'
            };
            
            // Convert logo to base64
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);
                const logoBase64 = canvas.toDataURL('image/png');
                this.generatePDF(log, orgSettings, logoBase64);
            };
            img.onerror = () => {
                this.generatePDF(log, orgSettings, '');
            };
            img.src = orgSettings.logo;
        },
        generatePDF(log, orgSettings, logoBase64) {
            const content = `
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .org-header { border-bottom: 3px solid #ea580c; padding-bottom: 20px; margin-bottom: 25px; }
                        .org-header-flex { display: flex; align-items: center; gap: 25px; }
                        .org-logo { width: 120px; height: 120px; object-fit: contain; border: 3px solid #ea580c; border-radius: 10px; padding: 10px; background: white; }
                        .org-name { font-size: 28px; font-weight: bold; color: #1f2937; margin-bottom: 10px; }
                        .org-details { font-size: 13px; color: #4b5563; line-height: 1.8; }
                        .org-detail-item { margin-bottom: 6px; display: flex; align-items: center; }
                        .org-icon { color: #ea580c; margin-right: 10px; width: 16px; }
                        .header { background: linear-gradient(to right, #ea580c, #dc2626); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
                        .section { margin-bottom: 20px; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; }
                        .label { font-weight: bold; color: #6b7280; font-size: 12px; }
                        .value { color: #1f2937; margin-bottom: 10px; }
                        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
                        .user-card { display: flex; align-items: center; gap: 15px; background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                        .user-photo { width: 80px; height: 80px; border-radius: 50%; border: 3px solid white; }
                        .member-card { display: flex; align-items: center; gap: 15px; }
                        .member-photo { width: 60px; height: 60px; border-radius: 50%; border: 2px solid #6366f1; }
                        pre { background: #f3f4f6; padding: 10px; border-radius: 5px; font-size: 11px; }
                    </style>
                </head>
                <body>
                    <div class="org-header">
                        <div class="org-header-flex">
                            ${logoBase64 ? `<img src="${logoBase64}" class="org-logo" alt="Logo">` : ''}
                            <div style="flex: 1;">
                                <div class="org-name">${orgSettings.name}</div>
                                <div class="org-details">
                                    <div class="org-detail-item">
                                        <span class="org-icon">✉</span>
                                        <span>${orgSettings.email}</span>
                                    </div>
                                    <div class="org-detail-item">
                                        <span class="org-icon">☎</span>
                                        <span>${orgSettings.phone}</span>
                                    </div>
                                    <div class="org-detail-item">
                                        <span class="org-icon">📍</span>
                                        <span>${orgSettings.address}</span>
                                    </div>
                                    <div class="org-detail-item">
                                        <span class="org-icon">🌐</span>
                                        <span>${orgSettings.website}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header">
                        <h1>Audit Log Details</h1>
                        <p>Log ID: ${log.id}</p>
                    </div>
                    <div class="user-card">
                        <img src="${log.userPhoto}" class="user-photo" alt="${log.user}">
                        <div>
                            <h2 style="margin: 0 0 5px 0;">${log.user}</h2>
                            <p style="margin: 0; color: #6b7280;">${log.userRole}</p>
                            <p style="margin: 5px 0 0 0; font-size: 13px;">${log.userEmail} | ${log.userPhone}</p>
                        </div>
                    </div>
                    <div class="section">
                        <h3>Activity Summary</h3>
                        <div class="grid">
                            <div><span class="label">Timestamp:</span> <div class="value">${log.timestamp}</div></div>
                            <div><span class="label">Action:</span> <div class="value">${log.action}</div></div>
                            <div><span class="label">Module:</span> <div class="value">${log.module}</div></div>
                            <div><span class="label">IP Address:</span> <div class="value">${log.ip}</div></div>
                            <div><span class="label">Location:</span> <div class="value">${log.location}</div></div>
                            <div><span class="label">Status:</span> <div class="value">${log.status}</div></div>
                        </div>
                    </div>
                    <div class="section">
                        <h3>Description</h3>
                        <p>${log.details}</p>
                    </div>
                    ${log.affectedMember ? `
                    <div class="section">
                        <h3>Affected Member</h3>
                        <div class="member-card">
                            <img src="${log.affectedMember.photo}" class="member-photo" alt="${log.affectedMember.name}">
                            <div class="grid" style="flex: 1;">
                                <div><span class="label">Name:</span> <div class="value">${log.affectedMember.name}</div></div>
                                <div><span class="label">Member ID:</span> <div class="value">${log.affectedMember.id}</div></div>
                                <div><span class="label">Email:</span> <div class="value">${log.affectedMember.email}</div></div>
                                <div><span class="label">Phone:</span> <div class="value">${log.affectedMember.phone}</div></div>
                            </div>
                        </div>
                    </div>` : ''}
                    <div class="section">
                        <h3>Technical Details</h3>
                        <div class="grid">
                            <div><span class="label">Browser:</span> <div class="value">${log.browser}</div></div>
                            <div><span class="label">Device:</span> <div class="value">${log.device}</div></div>
                            <div><span class="label">Platform:</span> <div class="value">${log.platform}</div></div>
                            <div><span class="label">Duration:</span> <div class="value">${log.duration}</div></div>
                        </div>
                    </div>
                    <div class="section">
                        <h3>Changes Made</h3>
                        <pre>${JSON.stringify(log.changes, null, 2)}</pre>
                    </div>
                </body>
                </html>
            `;
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write(content);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }
    }
}
</script>

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
