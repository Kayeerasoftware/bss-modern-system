@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left text-indigo-600"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">User Details</h2>
                <p class="text-gray-600 text-sm">View user account information</p>
            </div>
        </div>
        <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all font-bold transform hover:scale-105">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
    </div>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center overflow-hidden border-4 border-white shadow-2xl">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=128&background=random&bold=true" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 rounded-full border-4 border-white"></div>
                    </div>
                    <div class="text-center md:text-left">
                        <h3 class="text-3xl font-bold text-white mb-2">{{ $user->name }}</h3>
                        <p class="text-white/90 text-lg mb-3">{{ $user->email }}</p>
                        <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                            <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold">
                                <i class="fas fa-user-tag mr-1"></i>{{ ucfirst($user->role) }}
                            </span>
                            <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold">
                                <i class="fas fa-circle text-green-400 mr-1 text-xs"></i>{{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border-2 border-indigo-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-xl">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Personal Information</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-user-circle text-indigo-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Full Name</p>
                                    <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-envelope text-purple-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="font-bold text-gray-900">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-phone text-green-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Phone</p>
                                    <p class="font-bold text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-map-marker-alt text-red-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Location</p>
                                    <p class="font-bold text-gray-900">{{ $user->location ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border-2 border-purple-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl">
                                <i class="fas fa-shield-alt text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Account Information</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-user-tag text-indigo-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Role</p>
                                    <p class="font-bold text-gray-900">{{ ucfirst($user->role) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-circle text-green-600 w-5 text-xs"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Status</p>
                                    <p class="font-bold text-gray-900">{{ $user->is_active ? 'Active' : 'Inactive' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar-plus text-blue-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Joined</p>
                                    <p class="font-bold text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-clock text-orange-600 w-5"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Last Updated</p>
                                    <p class="font-bold text-gray-900">{{ $user->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->member)
                <div class="mt-6 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border-2 border-blue-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-xl">
                            <i class="fas fa-id-card text-white"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">Linked Member Account</h4>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Member ID</p>
                            <p class="font-bold text-gray-900">{{ $user->member->member_id }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Savings</p>
                            <p class="font-bold text-gray-900">{{ number_format($user->member->savings, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Loan</p>
                            <p class="font-bold text-gray-900">{{ number_format($user->member->loan, 2) }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.members.show', $user->member->id) }}" class="inline-block px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:shadow-lg transition-all text-sm font-bold">
                                <i class="fas fa-eye mr-1"></i>View Member
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 mt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.users.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="px-8 py-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-edit mr-2"></i>Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
