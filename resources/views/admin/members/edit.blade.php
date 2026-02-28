@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.members.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-arrow-left text-blue-600"></i>
        </a>
        <div>
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Edit Member</h2>
            <p class="text-gray-600 text-sm">Update member information - ID: {{ $member->member_id }}</p>
        </div>
    </div>

    <form action="{{ route('admin.members.update', $member->id) }}" method="POST" enctype="multipart/form-data" class="max-w-5xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Profile Picture Section -->
            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-8 text-center picture-drop-zone" data-member-id="{{ $member->id }}">
                <div class="flex justify-center mb-4">
                    <div class="relative group picture-container" data-member-id="{{ $member->id }}">
                        <div class="w-32 h-32 rounded-full bg-white flex items-center justify-center overflow-hidden border-4 border-white shadow-2xl">
                            @if($member->profile_picture_url)
                                <img id="preview" src="{{ $member->profile_picture_url }}" alt="{{ $member->full_name }}" class="w-full h-full object-cover">
                            @else
                                <img id="preview" src="" alt="" class="w-full h-full object-cover hidden">
                                <i id="placeholder" class="fas fa-user text-gray-400 text-5xl"></i>
                            @endif
                        </div>
                        
                        <!-- Upload/Camera Button -->
                        <label for="profile_picture" class="absolute bottom-0 right-0 bg-white text-blue-600 p-3 rounded-full cursor-pointer hover:bg-blue-50 transition-all shadow-lg transform group-hover:scale-110">
                            <i class="fas fa-camera"></i>
                        </label>
                        
                        <!-- Delete Button (only show if picture exists) -->
                        @if($member->profile_picture_url)
                            <button type="button" id="delete-btn" class="absolute top-0 right-0 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-all shadow-lg transform group-hover:scale-110">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        @endif
                        
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden" onchange="previewImage(event)">
                        
                        <!-- Upload Progress Bar -->
                        <div id="upload-progress" class="absolute bottom-0 left-0 right-0 bg-blue-500 h-1 rounded-full transition-all duration-300" style="display: none; width: 0%;"></div>
                    </div>
                </div>
                
                <h3 class="text-white text-xl font-bold">{{ $member->full_name }}</h3>
                <p class="text-white/80 text-sm mb-2">Update profile picture</p>
                <p class="text-white/60 text-xs">Drag & drop an image here or click the camera icon</p>
                <p class="text-white/60 text-xs">Supported: JPEG, PNG, GIF, WebP (Max: 5MB, Min: 100x100px)</p>
                
                @error('profile_picture')
                    <p class="text-red-200 text-xs mt-2 flex items-center justify-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </p>
                @enderror
                
                <!-- Picture Info -->
                @if($member->profile_picture_url)
                    <div class="mt-3 text-white/70 text-xs">
                        <div class="flex justify-center gap-4">
                            <span><i class="fas fa-image mr-1"></i>{{ pathinfo($member->profile_picture, PATHINFO_EXTENSION) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- Personal Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-blue-600 to-purple-600">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Personal Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-id-card text-gray-600"></i>
                                Member ID
                            </label>
                            <input type="text" value="{{ $member->member_id }}" disabled
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user-circle text-blue-600"></i>
                                Full Name *
                            </label>
                            <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">
                            @error('full_name')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-envelope text-purple-600"></i>
                                Email Address *
                            </label>
                            <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm">
                            @error('email')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-phone text-green-600"></i>
                                Contact Number *
                            </label>
                            <input type="text" name="contact" value="{{ old('contact', $member->contact) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-sm">
                            @error('contact')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                Location *
                            </label>
                            <input type="text" name="location" value="{{ old('location', $member->location) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all text-sm">
                            @error('location')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-briefcase text-orange-600"></i>
                                Occupation *
                            </label>
                            <input type="text" name="occupation" value="{{ old('occupation', $member->occupation) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm">
                            @error('occupation')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-purple-600 to-pink-600">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-lock text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Account & Security</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-user-tag text-indigo-600"></i>
                                Member Roles *
                            </label>
                            <div class="space-y-3 p-4 border-2 border-gray-200 rounded-xl bg-gray-50">
                                @php
                                    $memberRoles = $member->roles_list ?? [];
                                    $availableRoles = [
                                        'client' => 'Client',
                                        'shareholder' => 'Shareholder',
                                        'cashier' => 'Cashier',
                                        'td' => 'Technical Director',
                                        'ceo' => 'CEO'
                                    ];
                                @endphp
                                @foreach($availableRoles as $roleValue => $roleLabel)
                                    <label class="flex items-center gap-3 cursor-pointer hover:bg-white p-2 rounded-lg transition-all">
                                        <input type="checkbox" name="roles[]" value="{{ $roleValue }}" 
                                               {{ in_array($roleValue, $memberRoles) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">{{ $roleLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-star text-amber-500"></i>
                                    Default Role *
                                </label>
                                <select name="default_role" class="w-full px-3 py-2 text-sm border-2 border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                                    @foreach($availableRoles as $roleValue => $roleLabel)
                                        <option value="{{ $roleValue }}" {{ old('default_role', $member->role ?? 'client') === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                                    @endforeach
                                </select>
                                @error('default_role')
                                    <p class="text-xs text-red-600 flex items-center gap-1 mt-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Select one or more roles for this member</p>
                            @error('roles')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-key text-pink-600"></i>
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="Leave blank to keep current"
                                       class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm">
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-pink-600">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500">Leave blank to keep current password</p>
                            @error('password')
                                <p class="text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Financial Information (Read-only) -->
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b-2 border-gradient-to-r from-green-600 to-teal-600">
                        <div class="bg-gradient-to-r from-green-600 to-teal-600 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-coins text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Financial Information</h3>
                        <span class="text-xs bg-gray-100 px-3 py-1 rounded-full text-gray-600">Read-only</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-piggy-bank text-green-600"></i>
                                Savings
                            </label>
                            <input type="text" value="{{ number_format($member->savings, 2) }}" disabled
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-hand-holding-usd text-teal-600"></i>
                                Loan
                            </label>
                            <input type="text" value="{{ number_format($member->loan, 2) }}" disabled
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-wallet text-blue-600"></i>
                                Balance
                            </label>
                            <input type="text" value="{{ number_format($member->balance, 2) }}" disabled
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <i class="fas fa-money-check-alt text-indigo-600"></i>
                                Savings Balance
                            </label>
                            <input type="text" value="{{ number_format($member->savings_balance, 2) }}" disabled
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('admin.members.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-bold text-center transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Update Member
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('js/member-picture-manager.js') }}"></script>
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file before preview
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPEG, PNG, GIF, WebP)');
            event.target.value = '';
            return;
        }
        
        if (file.size > maxSize) {
            alert('File size must be less than 5MB');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview');
            const placeholder = document.getElementById('placeholder');
            
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            
            // Show delete button
            const deleteBtn = document.getElementById('delete-btn');
            if (deleteBtn) deleteBtn.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = document.getElementById(id + '-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Add drag and drop styling
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.querySelector('.picture-drop-zone');
    
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-4', 'border-dashed', 'border-white', 'bg-white/10');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-4', 'border-dashed', 'border-white', 'bg-white/10');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-4', 'border-dashed', 'border-white', 'bg-white/10');
        });
    }
});
</script>
@endsection

