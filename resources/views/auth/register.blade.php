@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="max-w-sm w-full">
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header with Logo -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3 text-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="BSS Logo" class="w-16 h-16 mx-auto mb-1 rounded-lg shadow-lg">
            <h1 class="text-lg font-bold text-white">BSS Investment Group</h1>
        </div>

        <!-- Register Form -->
        <div class="p-4">
            <!-- Success Message with Progress Bar -->
            @if (session('register_success'))
                <div id="successMessage" class="mb-3 relative overflow-hidden bg-green-50 border-2 border-green-500 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        <span class="text-green-800 font-bold text-sm">Registered successfully as {{ session('register_role') }}</span>
                    </div>
                    <div class="h-1 bg-green-200 rounded-full overflow-hidden">
                        <div id="progressBar" class="h-full bg-green-600"></div>
                    </div>
                </div>
                <script>
                    const progressBar = document.getElementById('progressBar');
                    progressBar.style.width = '100%';
                    progressBar.style.transition = 'width 3s linear';
                    
                    setTimeout(() => {
                        progressBar.style.width = '0%';
                    }, 10);
                    
                    setTimeout(() => {
                        window.location.href = '{{ route('dashboard') }}';
                    }, 3000);
                </script>
            @endif

            <h2 class="text-base font-bold text-gray-800 mb-2">Create Account</h2>

            @error('email')
                @if(str_contains($message, 'already been taken'))
                    <!-- Email Already Taken Error -->
                    <div class="mb-3 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-4 bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-blue-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-blue-500 to-cyan-600 p-2 rounded-full shadow-lg">
                                            <i class="fas fa-envelope text-white text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-extrabold text-blue-900 mb-1 flex items-center gap-1">
                                        <i class="fas fa-user-check"></i>
                                        Email Already Registered
                                    </h3>
                                    <p class="text-xs font-semibold text-blue-800 mb-2">This email address is already associated with an existing account.</p>
                                    <div class="mt-2 p-2 bg-white/60 rounded-lg border border-blue-200">
                                        <p class="text-xs text-blue-700 leading-relaxed">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            <strong>What to do:</strong><br>
                                            • Try logging in with this email instead<br>
                                            • Use a different email address<br>
                                            • Reset your password if you forgot it
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center gap-1 text-xs text-blue-600">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <span class="font-semibold">Already have an account? <a href="{{ route('login') }}" class="text-blue-700 underline hover:text-blue-900 font-bold">Sign in here</a></span>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-3').remove()" class="flex-shrink-0 text-blue-400 hover:text-blue-700 transition-colors p-1 hover:bg-blue-100 rounded-full">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @enderror

            @error('password')
                @if(str_contains($message, 'at least 6 characters'))
                    <!-- Password Too Short Error -->
                    <div class="mb-3 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-4 bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-red-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-red-500 to-orange-600 p-2 rounded-full shadow-lg">
                                            <i class="fas fa-lock text-white text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-extrabold text-red-900 mb-1 flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Password Too Short
                                    </h3>
                                    <p class="text-xs font-semibold text-red-800 mb-2">The password must be at least 6 characters.</p>
                                    <div class="mt-2 p-2 bg-white/60 rounded-lg border border-red-200">
                                        <p class="text-xs text-red-700 leading-relaxed">
                                            <i class="fas fa-shield-alt mr-1"></i>
                                            <strong>Security requirements:</strong><br>
                                            • Minimum 6 characters required<br>
                                            • Use a mix of letters and numbers<br>
                                            • Avoid common passwords
                                        </p>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-3').remove()" class="flex-shrink-0 text-red-400 hover:text-red-700 transition-colors p-1 hover:bg-red-100 rounded-full">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @elseif(str_contains($message, 'confirmation does not match'))
                    <!-- Password Mismatch Error -->
                    <div class="mb-3 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-pink-500 to-rose-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-4 bg-gradient-to-br from-pink-50 to-rose-50 border-2 border-pink-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-pink-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-pink-500 to-rose-600 p-2 rounded-full shadow-lg">
                                            <i class="fas fa-key text-white text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-extrabold text-pink-900 mb-1 flex items-center gap-1">
                                        <i class="fas fa-times-circle"></i>
                                        Password Mismatch
                                    </h3>
                                    <p class="text-xs font-semibold text-pink-800 mb-2">The passwords you entered do not match.</p>
                                    <div class="mt-2 p-2 bg-white/60 rounded-lg border border-pink-200">
                                        <p class="text-xs text-pink-700 leading-relaxed">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>How to fix:</strong><br>
                                            • Make sure both password fields contain the exact same text<br>
                                            • Check for extra spaces or typos<br>
                                            • Use the eye icon to view your passwords
                                        </p>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-3').remove()" class="flex-shrink-0 text-pink-400 hover:text-pink-700 transition-colors p-1 hover:bg-pink-100 rounded-full">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @enderror

            @error('role')
                @if($message == 'The role field is required.')
                    <!-- Role Not Selected Error -->
                    <div class="mb-3 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-yellow-500 to-orange-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-4 bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-400 rounded-2xl shadow-2xl animate-bounce-slow">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-yellow-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-yellow-500 to-orange-600 p-2 rounded-full shadow-lg">
                                            <i class="fas fa-hand-pointer text-white text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-extrabold text-yellow-900 mb-1 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Role Selection Required
                                    </h3>
                                    <p class="text-xs font-semibold text-yellow-800 mb-2">Please select a role to continue with registration.</p>
                                    <div class="mt-2 p-2 bg-white/60 rounded-lg border border-yellow-200">
                                        <p class="text-xs text-yellow-700 leading-relaxed">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            <strong>How to fix:</strong> Click the dropdown above and choose your role marked with <span class="text-green-600 font-bold">[Active role]</span>.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-3').remove()" class="flex-shrink-0 text-yellow-400 hover:text-yellow-700 transition-colors p-1 hover:bg-yellow-100 rounded-full">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @elseif(str_contains($message, 'Registration for this role is currently disabled'))
                    <!-- Registration Blocked Error -->
                    <div class="mb-3 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-4 bg-gradient-to-br from-orange-50 to-red-50 border-2 border-orange-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-orange-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-orange-500 to-red-600 p-2 rounded-full shadow-lg">
                                            <i class="fas fa-user-slash text-white text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-extrabold text-orange-900 mb-1 flex items-center gap-1">
                                        <i class="fas fa-ban"></i>
                                        Registration Blocked
                                    </h3>
                                    <p class="text-xs font-semibold text-orange-800 mb-2">{{ $message }}</p>
                                    <div class="mt-2 p-2 bg-white/60 rounded-lg border border-orange-200">
                                        <p class="text-xs text-orange-700 leading-relaxed">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Why is this happening?</strong><br>
                                            The administrator has disabled new registrations for this role. This is a separate control from role activation.
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center gap-1 text-xs text-orange-600">
                                        <i class="fas fa-phone-alt"></i>
                                        <span class="font-semibold">
                                            Contact admin
                                            @if(isset($adminPhone) && $adminPhone)
                                                at <a href="tel:{{ $adminPhone }}" class="text-orange-700 underline hover:text-orange-900 font-bold">{{ $adminPhone }}</a>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-3').remove()" class="flex-shrink-0 text-orange-400 hover:text-orange-700 transition-colors p-1 hover:bg-orange-100 rounded-full">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Role Inactive Error -->
                    <div class="mb-3 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-pink-500 opacity-10 animate-pulse"></div>
                        <div class="relative p-4 bg-gradient-to-br from-red-50 to-pink-50 border-2 border-red-400 rounded-2xl shadow-2xl animate-shake">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-red-500 rounded-full blur-md opacity-50 animate-ping"></div>
                                        <div class="relative bg-gradient-to-br from-red-500 to-red-600 p-2 rounded-full shadow-lg">
                                            <i class="fas fa-ban text-white text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-extrabold text-red-900 mb-1 flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Access Denied - Role Inactive
                                    </h3>
                                    <p class="text-xs font-semibold text-red-800 mb-2">{{ $message }}</p>
                                    <div class="mt-2 p-2 bg-white/60 rounded-lg border border-red-200">
                                        <p class="text-xs text-red-700 leading-relaxed">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Why is this happening?</strong><br>
                                            The system administrator has temporarily disabled this role for security or maintenance purposes.
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center gap-1 text-xs text-red-600">
                                        <i class="fas fa-phone-alt"></i>
                                        <span class="font-semibold">
                                            Contact admin immediately
                                            @if(isset($adminPhone) && $adminPhone)
                                                at <a href="tel:{{ $adminPhone }}" class="text-red-700 underline hover:text-red-900 font-bold">{{ $adminPhone }}</a>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <button type="button" onclick="this.closest('.mb-3').remove()" class="flex-shrink-0 text-red-400 hover:text-red-700 transition-colors p-1 hover:bg-red-100 rounded-full">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @enderror

            <style>
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                    20%, 40%, 60%, 80% { transform: translateX(5px); }
                }
                .animate-shake {
                    animation: shake 0.5s ease-in-out;
                }
                @keyframes bounce-slow {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-8px); }
                }
                .animate-bounce-slow {
                    animation: bounce-slow 1s ease-in-out 2;
                }
                @keyframes ping {
                    75%, 100% {
                        transform: scale(1.5);
                        opacity: 0;
                    }
                }
            </style>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-2">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-2 py-1.5 text-xs border rounded focus:outline-none focus:border-blue-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-2">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-2 py-1.5 text-xs border rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-2">
                    <label class="block text-green-600 text-xs font-semibold mb-1">Select Allowed role</label>
                    <div class="relative">
                        <input type="hidden" name="role" id="roleInput" required>
                        <div class="w-full px-2 py-1.5 text-xs border rounded cursor-pointer bg-white" id="roleDropdown">
                            <span id="selectedRole" class="text-gray-500">Select a role...</span>
                        </div>
                        <div id="roleOptions" class="hidden absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-40 overflow-y-auto">
                            @php
                                $roles = [
                                    'client' => 'Client/Member',
                                    'shareholder' => 'Shareholder',
                                    'cashier' => 'Cashier',
                                    'td' => 'Technical Director',
                                    'ceo' => 'CEO',
                                    'admin' => 'Administrator'
                                ];
                            @endphp
                            @foreach($roles as $roleKey => $roleLabel)
                                @php
                                    $isRegistrationAllowed = ($registrationAllowed[$roleKey] ?? 1) == 1;
                                @endphp
                                <div class="px-2 py-1.5 hover:bg-gray-100 cursor-pointer text-xs" data-value="{{ $roleKey }}">
                                    <span class="text-gray-800">{{ $roleLabel }}</span>
                                    <span class="font-semibold" style="color: {{ $isRegistrationAllowed ? '#16a34a' : '#dc2626' }};">[{{ $isRegistrationAllowed ? 'Allowed role' : 'Not Allowed role' }}]</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <script>
                    const dropdown = document.getElementById('roleDropdown');
                    const options = document.getElementById('roleOptions');
                    const input = document.getElementById('roleInput');
                    const selected = document.getElementById('selectedRole');

                    dropdown.addEventListener('click', () => {
                        options.classList.toggle('hidden');
                    });

                    document.querySelectorAll('#roleOptions > div').forEach(option => {
                        option.addEventListener('click', () => {
                            input.value = option.dataset.value;
                            selected.innerHTML = option.innerHTML;
                            selected.classList.remove('text-gray-500');
                            options.classList.add('hidden');
                        });
                    });

                    document.addEventListener('click', (e) => {
                        if (!dropdown.contains(e.target) && !options.contains(e.target)) {
                            options.classList.add('hidden');
                        }
                    });
                </script>

                <div class="mb-2">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-2 py-1.5 text-xs border rounded focus:outline-none focus:border-blue-500 pr-8">
                        <button type="button" onclick="togglePassword()" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye text-xs" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-2">
                    <label class="block text-gray-700 text-xs font-semibold mb-1">Confirm</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-2 py-1.5 text-xs border rounded focus:outline-none focus:border-blue-500 pr-8">
                        <button type="button" onclick="togglePasswordConfirm()" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye text-xs" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-1.5 rounded-lg font-semibold text-xs hover:from-blue-700 hover:to-purple-700 transition-all mt-2">
                    Create Account
                </button>
            </form>

            <p class="text-center text-xs text-gray-600 mt-2">
                Have an account? 
                <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:text-blue-800 hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
