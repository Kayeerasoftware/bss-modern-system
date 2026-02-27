@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('shareholder.members') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-purple-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">Member Profile</h2>
                <p class="text-gray-600 text-sm">Complete member information</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto">
        <!-- Profile Header Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 mb-6">
            <div class="relative">
                <!-- Cover Background -->
                <div class="h-48 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600"></div>
                
                <!-- Profile Info -->
                <div class="relative px-6 pb-6">
                    <div class="flex flex-col md:flex-row md:items-end gap-6">
                        <!-- Profile Picture -->
                        <div class="-mt-20">
                            @if($member->profile_picture_url)
                                <img src="{{ $member->profile_picture_url }}" alt="{{ $member->full_name }}" class="w-40 h-40 rounded-3xl object-cover border-8 border-white shadow-2xl">
                            @else
                                <div class="w-40 h-40 rounded-3xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center border-8 border-white shadow-2xl">
                                    <span class="text-6xl font-bold text-white">{{ substr($member->full_name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Member Info -->
                        <div class="flex-1 md:mb-4">
                            <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $member->full_name }}</h3>
                            <div class="flex flex-wrap gap-3 mb-3">
                                <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-bold">
                                    <i class="fas fa-id-card mr-1"></i>{{ $member->member_id }}
                                </span>
                                <span class="px-4 py-2 bg-pink-100 text-pink-800 rounded-full text-sm font-bold">
                                    <i class="fas fa-user-tag mr-1"></i>{{ ucfirst($member->role ?? 'Client') }}
                                </span>
                                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-bold">
                                    <i class="fas fa-circle text-xs mr-1"></i>Active
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm">
                                <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                                Member since {{ $member->created_at->format('F d, Y') }} ({{ $member->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-piggy-bank text-white text-xl"></i>
                    </div>
                </div>
                @if(\App\Models\Setting::get('shareholder_hide_savings', 0) == 1 && $member->user_id !== auth()->id())
                    <p class="text-2xl font-bold text-red-600 mb-1 bg-red-50 px-3 py-2 rounded-lg">HIDDEN</p>
                @else
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($member->savings ?? 0) }}</p>
                @endif
                <p class="text-sm text-gray-600 font-semibold">Total Savings</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-pie text-white text-xl"></i>
                    </div>
                </div>
                @if(\App\Models\Setting::get('shareholder_hide_shares', 0) == 1 && $member->user_id !== auth()->id())
                    <p class="text-2xl font-bold text-red-600 mb-1 bg-red-50 px-3 py-2 rounded-lg">HIDDEN</p>
                @else
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ $member->shares->count() }}</p>
                @endif
                <p class="text-sm text-gray-600 font-semibold">Total Shares</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $member->transactions->count() }}</p>
                <p class="text-sm text-gray-600 font-semibold">Transactions</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $member->created_at->diffInDays(now()) }}</p>
                <p class="text-sm text-gray-600 font-semibold">Days Active</p>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-3xl shadow-2xl p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-address-card text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Contact Information</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold mb-1">Email Address</p>
                            <p class="text-sm font-bold text-gray-900">{{ $member->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-pink-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold mb-1">Phone Number</p>
                            <p class="text-sm font-bold text-gray-900">{{ $member->contact ?? $member->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-indigo-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold mb-1">Location</p>
                            <p class="text-sm font-bold text-gray-900">{{ $member->location ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-3xl shadow-2xl p-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Recent Activity</h3>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($member->transactions->take(8) as $transaction)
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-circle text-purple-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Activity</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>Done
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-medium">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

