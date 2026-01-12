@extends('layouts.app')

@section('title', 'Login - BSS System')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-8">
        <div class="bg-white rounded-2xl shadow-2xl p-6 lg:p-8 w-full max-w-md">
            <div class="text-center mb-6 lg:mb-8">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">BSS System</h1>
                <p class="text-gray-600 text-sm lg:text-base">Sign in to your account</p>
            </div>

            <form id="loginForm" class="space-y-4 lg:space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-3 py-2 lg:px-4 lg:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm lg:text-base">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-3 py-2 lg:px-4 lg:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm lg:text-base">
                </div>

                <button type="submit" id="loginBtn" 
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-2 lg:py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 font-medium text-sm lg:text-base">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>

            <div id="message" class="mt-4 p-3 rounded-lg hidden"></div>

            <div class="mt-4 lg:mt-6 text-center">
                <p class="text-gray-600 text-sm">Test Credentials:</p>
                <p class="text-xs lg:text-sm text-gray-500">Admin: admin@bss.com / admin123</p>
                <p class="text-xs lg:text-sm text-gray-500">Member: member@bss.com / member123</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.getElementById('loginForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const btn = document.getElementById('loginBtn');
                const messageDiv = document.getElementById('message');
                
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...';
                messageDiv.classList.add('hidden'); // Hide previous messages
                
                try {
                    const response = await fetch('/api/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            email: document.getElementById('email').value,
                            password: document.getElementById('password').value
                        })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        messageDiv.className = 'mt-4 p-3 rounded-lg bg-green-100 text-green-800';
                        messageDiv.textContent = 'Login successful! Redirecting...';
                        messageDiv.classList.remove('hidden');
                        
                        setTimeout(() => {
                            window.location.href = '/dashboard'; // Redirect to dashboard
                        }, 1000);
                    } else {
                        throw new Error(result.message || 'Login failed');
                    }
                } catch (error) {
                    messageDiv.className = 'mt-4 p-3 rounded-lg bg-red-100 text-red-800';
                    messageDiv.textContent = error.message;
                    messageDiv.classList.remove('hidden');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Sign In';
                }
            });
        });
    </script>

    <style>
        .form-input {
            @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400;
            transition: all 0.2s ease-in-out;
        }
        .form-input:focus {
            @apply border-blue-500 ring-2 ring-blue-500 ring-opacity-50 outline-none;
        }
    </style>
@endsection