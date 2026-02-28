@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-3 md:p-6">
    <!-- Header Section -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-blue-600 to-purple-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-users text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-1 md:mb-2">Member Management</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Manage members, roles, and activities</p>
                </div>
            </div>
            <div class="flex gap-1.5 md:gap-2 w-full md:w-auto">
                <a href="{{ route('admin.members.index', array_merge(request()->except('page', 'trash'), ['trash' => 'active'])) }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 rounded-lg md:rounded-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105 {{ ($trashFilter ?? 'active') === 'active' ? 'bg-gradient-to-r from-emerald-600 to-green-600 text-white' : 'bg-white text-gray-700 border border-gray-200' }}">
                    <i class="fas fa-list"></i><span>Active</span>
                </a>
                <a href="{{ route('admin.members.index', array_merge(request()->except('page', 'trash'), ['trash' => 'only'])) }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 rounded-lg md:rounded-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105 {{ ($trashFilter ?? 'active') === 'only' ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white' : 'bg-white text-gray-700 border border-gray-200' }}">
                    <i class="fas fa-trash-restore"></i><span>Trash</span>
                </a>
                <a href="{{ route('admin.members.pictures.bulk-upload-form') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-images"></i><span class="hidden sm:inline">Bulk Pictures</span><span class="sm:hidden">Pictures</span>
                </a>
                <a href="{{ route('admin.bio-data.create') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-id-card"></i><span class="hidden sm:inline">Bio Data</span>
                </a>
                <a href="{{ route('admin.members.create') }}" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-user-plus"></i><span class="hidden sm:inline">Add Member</span><span class="sm:hidden">Add</span>
                </a>
                <button class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-white text-gray-700 rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 shadow-md border border-gray-200 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-file-export"></i><span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-green-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Members data...</span>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-xl shadow-md animate-fade-in">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-2xl mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl shadow-md animate-fade-in">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 md:mb-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Members</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $memberStats['totalMembers'] ?? $members->total() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-users text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-[8px] md:text-[10px] font-medium mb-0.5">Active Members</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $memberStats['activeMembers'] ?? $members->where('status', 'active')->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-user-check text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Savings</p>
                    <h3 class="text-base md:text-xl font-bold">{{ number_format($memberStats['totalSavings'] ?? $members->sum('savings')) }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-piggy-bank text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-[8px] md:text-[10px] font-medium mb-0.5">New This Month</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $memberStats['newThisMonth'] ?? $members->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-user-plus text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Search and Filters -->
    <div class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl shadow-lg border border-blue-100 overflow-hidden mb-4">
        <form method="GET" action="{{ route('admin.members.index') }}" x-data="{ showAdvanced: false }">
            <input type="hidden" name="trash" value="{{ $trashFilter ?? 'active' }}">
            <div class="bg-white/60 backdrop-blur-sm p-3">
                <!-- Basic Search Section -->
                <div class="bg-white/80 rounded-xl p-2.5 border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                        <div class="md:col-span-4 relative">
                            <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-blue-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search members..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-user-tag absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                            <select name="role" class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                                <option value="td" {{ request('role') == 'td' ? 'selected' : '' }}>TD</option>
                                <option value="ceo" {{ request('role') == 'ceo' ? 'selected' : '' }}>CEO</option>
                                <option value="shareholder" {{ request('role') == 'shareholder' ? 'selected' : '' }}>Shareholder</option>
                                <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 relative">
                            <i class="fas fa-circle absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-[8px]"></i>
                            <select name="status" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 relative">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="w-full px-2 py-1.5 text-xs bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-lg hover:shadow-md transition-all flex items-center justify-center gap-1">
                                <i class="fas fa-sliders-h"></i>
                                <i class="fas fa-chevron-down text-[8px] transition-transform" :class="showAdvanced ? 'rotate-180' : ''"></i>
                            </button>
                        </div>
                        <div class="md:col-span-3 flex gap-1.5">
                            <button type="submit" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-semibold">
                                <i class="fas fa-search mr-1"></i>Search
                            </button>
                            <a href="{{ route('admin.members.index') }}" class="px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters Section -->
                    <div x-show="showAdvanced" x-collapse class="mt-2 pt-2 border-t border-blue-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div class="flex gap-1">
                                <input type="number" name="savings_min" value="{{ request('savings_min') }}" placeholder="Min Savings" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                                <input type="number" name="savings_max" value="{{ request('savings_max') }}" placeholder="Max" class="w-full px-2 py-1.5 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            </div>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="w-full px-2 py-1.5 text-xs border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white" @change="$el.form.submit()">
                            <select name="sort" class="w-full px-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none bg-white" @change="$el.form.submit()">
                                <option value="">Sort By</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="savings_high" {{ request('sort') == 'savings_high' ? 'selected' : '' }}>Savings (High-Low)</option>
                                <option value="savings_low" {{ request('sort') == 'savings_low' ? 'selected' : '' }}>Savings (Low-High)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Members Grid/Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" id="members-table-container">
        <div class="overflow-x-auto">
            @include('admin.members.partials.table')
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const form = searchInput.closest('form');
    const tableContainer = document.getElementById('members-table-container');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch();
        }, 500);
    });

    function performSearch() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        fetch(`{{ route('admin.members.index') }}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.querySelector('.overflow-x-auto').innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
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
