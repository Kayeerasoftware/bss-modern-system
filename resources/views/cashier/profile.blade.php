@extends('layouts.cashier')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
    <!-- Animated Cover Photo -->
    <div class="h-80 bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-yellow-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/3 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
        </div>
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 40px 40px;"></div>
        
        <div class="absolute top-8 left-8 flex gap-3">
            <a href="{{ route('cashier.dashboard') }}" class="group w-12 h-12 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center hover:bg-white/20 transition-all duration-300 hover:scale-110 border border-white/20">
                <i class="fas fa-arrow-left text-white group-hover:-translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <div class="absolute bottom-8 left-8 text-white">
            <h1 class="text-5xl font-black mb-2 drop-shadow-2xl">Profile Settings</h1>
            <p class="text-white/80 text-lg font-medium">Manage your account and preferences</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-8 -mt-40 pb-16">
        <!-- Profile Card -->
        <div class="bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-white/50 p-10 mb-8">
            <div class="flex flex-col lg:flex-row gap-10 items-start">
                <!-- Profile Picture Section -->
                <div class="flex-shrink-0">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 rounded-[2rem] blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-tilt"></div>
                        <div class="relative w-48 h-48 rounded-[2rem] bg-gradient-to-br from-violet-500 via-purple-500 to-indigo-500 p-2">
                            <div class="w-full h-full rounded-[1.75rem] overflow-hidden bg-white">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=512&background=random&bold=true" alt="Profile" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>
                        <button onclick="document.getElementById('profilePictureInput').click()" class="absolute -bottom-4 -right-4 w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-2xl shadow-2xl hover:shadow-pink-500/50 transform hover:scale-110 hover:rotate-12 transition-all duration-300">
                            <i class="fas fa-camera text-xl"></i>
                            <input type="file" id="profilePictureInput" accept="image/*" class="hidden" onchange="showCropModal(event)">
                        </button>
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full border-4 border-white shadow-xl flex items-center justify-center">
                            <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <div class="mb-6">
                        <h2 class="text-5xl font-black bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent mb-3">{{ $user->name }}</h2>
                        <p class="text-2xl text-gray-600 font-semibold mb-4">{{ ucfirst($user->role) }}</p>
                        <div class="flex flex-wrap gap-3">
                            <span class="px-5 py-2 bg-gradient-to-r from-green-400 to-emerald-500 text-white rounded-full text-sm font-bold shadow-lg flex items-center gap-2">
                                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>Online Now
                            </span>
                            <span class="px-5 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full text-sm font-bold shadow-lg">
                                <i class="fas fa-shield-alt mr-2"></i>Full Access
                            </span>
                            <span class="px-5 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-sm font-bold shadow-lg">
                                <i class="fas fa-cash-register mr-2"></i>Cashier
                            </span>
                        </div>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-5">
                        <div class="group relative bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-4xl font-black">{{ $totalMembers }}</div>
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>
                                </div>
                                <div class="text-sm font-bold opacity-90">Total Members</div>
                                <div class="text-xs opacity-75 mt-1">Registered users</div>
                            </div>
                        </div>
                        
                        <div class="group relative bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-4xl font-black">{{ $totalTransactions }}</div>
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                        <i class="fas fa-exchange-alt text-2xl"></i>
                                    </div>
                                </div>
                                <div class="text-sm font-bold opacity-90">Transactions</div>
                                <div class="text-xs opacity-75 mt-1">All time total</div>
                            </div>
                        </div>
                        
                        <div class="group relative bg-gradient-to-br from-indigo-500 to-violet-500 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-4xl font-black">{{ $activeLoans }}</div>
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                        <i class="fas fa-hand-holding-usd text-2xl"></i>
                                    </div>
                                </div>
                                <div class="text-sm font-bold opacity-90">Active Loans</div>
                                <div class="text-xs opacity-75 mt-1">{{ $totalLoans }} total loans</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-white/50 overflow-hidden" x-data="{ activeTab: 'personal' }">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-3">
                <div class="flex gap-2">
                    <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl scale-105' : 'bg-white text-gray-600 hover:text-gray-900 hover:shadow-lg'" class="flex-1 px-6 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-user mr-2"></i>Personal
                    </button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl scale-105' : 'bg-white text-gray-600 hover:text-gray-900 hover:shadow-lg'" class="flex-1 px-6 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-shield-alt mr-2"></i>Security
                    </button>
                    <button @click="activeTab = 'preferences'" :class="activeTab === 'preferences' ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl scale-105' : 'bg-white text-gray-600 hover:text-gray-900 hover:shadow-lg'" class="flex-1 px-6 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-sliders-h mr-2"></i>Settings
                    </button>
                    <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl scale-105' : 'bg-white text-gray-600 hover:text-gray-900 hover:shadow-lg'" class="flex-1 px-6 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-clock mr-2"></i>Activity
                    </button>
                </div>
            </div>

            <div class="p-10">
                <!-- Personal Info Tab -->
                <div x-show="activeTab === 'personal'" x-transition class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Full Name</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Email Address</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" value="{{ $user->phone }}" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Location</label>
                            <input type="text" name="location" value="{{ $user->location }}" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium">
                        </div>
                        <div class="lg:col-span-2 space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Bio</label>
                            <textarea rows="4" name="bio" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium" placeholder="Tell us about yourself...">{{ $user->bio }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" class="px-8 py-4 bg-gray-100 text-gray-700 rounded-2xl font-bold hover:bg-gray-200 transition-all hover:scale-105">Cancel</button>
                        <button type="button" onclick="updateProfile()" class="px-8 py-4 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all hover:scale-105">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </div>

                <!-- Security Tab -->
                <div x-show="activeTab === 'security'" x-transition class="max-w-2xl space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Current Password</label>
                        <input type="password" name="current_password" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">New Password</label>
                        <input type="password" name="new_password" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Confirm Password</label>
                        <input type="password" name="new_password_confirmation" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all font-medium">
                    </div>
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 p-5 rounded-2xl">
                        <div class="flex gap-3">
                            <i class="fas fa-info-circle text-amber-600 text-xl mt-1"></i>
                            <div class="text-sm text-amber-900 font-medium">
                                <p class="font-bold mb-2">Password Requirements:</p>
                                <ul class="space-y-1">
                                    <li>✓ At least 8 characters</li>
                                    <li>✓ Uppercase & lowercase letters</li>
                                    <li>✓ Numbers & special characters</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="updatePassword()" class="w-full px-8 py-4 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all hover:scale-105">
                        <i class="fas fa-shield-alt mr-2"></i>Update Password
                    </button>
                </div>

                <!-- Preferences Tab -->
                <div x-show="activeTab === 'preferences'" x-transition class="space-y-5">
                    <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 border-2 border-blue-100 hover:border-blue-300 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center text-white">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">Email Notifications</p>
                                <p class="text-sm text-gray-600">Receive updates via email</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-16 h-8 bg-gray-300 peer-focus:ring-4 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-8 peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-violet-600 peer-checked:to-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border-2 border-purple-100 hover:border-purple-300 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white">
                                <i class="fas fa-sms text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">SMS Notifications</p>
                                <p class="text-sm text-gray-600">Get text message alerts</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-16 h-8 bg-gray-300 peer-focus:ring-4 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-8 peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-violet-600 peer-checked:to-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between bg-gradient-to-r from-indigo-50 to-violet-50 rounded-2xl p-6 border-2 border-indigo-100 hover:border-indigo-300 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-violet-500 rounded-2xl flex items-center justify-center text-white">
                                <i class="fas fa-moon text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">Dark Mode</p>
                                <p class="text-sm text-gray-600">Switch to dark theme</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-16 h-8 bg-gray-300 peer-focus:ring-4 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-8 peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-violet-600 peer-checked:to-indigo-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div x-show="activeTab === 'activity'" x-transition class="space-y-4">
                    @forelse($activities as $activity)
                    <div class="flex gap-5 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl hover:shadow-lg transition-all border-2 border-blue-100">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center flex-shrink-0 text-white shadow-lg">
                            <i class="fas fa-history text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-lg">{{ $activity->action }}</p>
                            <p class="text-gray-600 font-medium">{{ $activity->details }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($activity->timestamp)->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-medium">No activity logs found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Crop Modal -->
<div id="cropModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-violet-600 to-indigo-600 p-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-white"><i class="fas fa-crop-alt mr-2"></i>Crop Profile Picture</h3>
            <button onclick="closeCropModal()" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl transition-all">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4 bg-gray-100 rounded-2xl overflow-hidden" style="max-height: 400px;">
                <img id="cropImage" class="max-w-full">
            </div>
            <div class="flex gap-3 justify-end">
                <button onclick="closeCropModal()" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-xl font-bold transition-all">
                    Cancel
                </button>
                <button onclick="cropAndUpload()" class="px-6 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-check mr-2"></i>Crop & Upload
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -50px) scale(1.1); }
    50% { transform: translate(-20px, 20px) scale(0.9); }
    75% { transform: translate(50px, 50px) scale(1.05); }
}
.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
@keyframes tilt {
    0%, 100% { transform: rotate(-1deg); }
    50% { transform: rotate(1deg); }
}
.animate-tilt { animation: tilt 3s ease-in-out infinite; }
</style>

<script>
let cropper = null;
let currentFile = null;

function showCropModal(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    currentFile = file;
    const reader = new FileReader();
    reader.onload = function(e) {
        const image = document.getElementById('cropImage');
        image.src = e.target.result;
        document.getElementById('cropModal').classList.remove('hidden');
        
        if (cropper) cropper.destroy();
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 2,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    };
    reader.readAsDataURL(file);
}

function closeCropModal() {
    document.getElementById('cropModal').classList.add('hidden');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('profilePictureInput').value = '';
}

function cropAndUpload() {
    if (!cropper) return;
    
    cropper.getCroppedCanvas({
        width: 512,
        height: 512,
        imageSmoothingQuality: 'high'
    }).toBlob(function(blob) {
        const formData = new FormData();
        formData.append('profile_picture', blob, currentFile.name);
        
        fetch('{{ route("cashier.profile.picture") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeCropModal();
                location.reload();
            } else {
                alert(data.message || 'Error uploading picture');
            }
        })
        .catch(error => {
            console.error(error);
            alert('Error uploading picture');
        });
    }, 'image/jpeg', 0.95);
}

function updateProfile() {
    const formData = new FormData();
    formData.append('name', document.querySelector('input[name="name"]').value);
    formData.append('email', document.querySelector('input[name="email"]').value);
    formData.append('phone', document.querySelector('input[name="phone"]').value);
    formData.append('location', document.querySelector('input[name="location"]').value);
    formData.append('bio', document.querySelector('textarea[name="bio"]').value);
    formData.append('_method', 'PUT');
    
    fetch('{{ route("cashier.profile.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Error updating profile');
        }
    })
    .catch(error => {
        console.error(error);
        alert('Error updating profile');
    });
}

function updatePassword() {
    const formData = {
        current_password: document.querySelector('input[name="current_password"]').value,
        new_password: document.querySelector('input[name="new_password"]').value,
        new_password_confirmation: document.querySelector('input[name="new_password_confirmation"]').value
    };
    
    fetch('{{ route("cashier.profile.password") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.querySelectorAll('input[type="password"]').forEach(input => input.value = '');
        } else {
            alert(data.message || 'Error updating password');
        }
    })
    .catch(error => {
        console.error(error);
        alert('Error updating password');
    });
}


</script>
@endsection
