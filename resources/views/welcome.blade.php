@extends('layouts.app')

@section('title', 'BSS Investment Group')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
    <!-- Hero Section -->
    <section class="min-h-screen flex items-center justify-center text-center text-white relative">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                BSS <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">Investment</span> Group
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-white/90 max-w-3xl mx-auto">
                Your trusted partner in financial growth and community development
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/dashboard" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:scale-105 transition-all">
                    Access Dashboard
                </a>
                <a href="#features" class="border-2 border-white/30 hover:border-white text-white px-8 py-4 rounded-full text-lg font-semibold transition-all">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Our Services</h2>
                <p class="text-xl text-white/80">Comprehensive financial solutions for your growth</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl border border-white/20">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-piggy-bank text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Savings Management</h3>
                    <p class="text-white/80">Secure savings accounts with competitive interest rates</p>
                </div>
                <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl border border-white/20">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-money-bill-wave text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Loan Services</h3>
                    <p class="text-white/80">Flexible loan options for personal and business needs</p>
                </div>
                <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl border border-white/20">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Investment Opportunities</h3>
                    <p class="text-white/80">Grow your wealth through strategic investments</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Get Started Today</h2>
                <p class="text-xl text-white/80">Join our community of successful investors</p>
            </div>
            <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl border border-white/20">
                <form action="/contact" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="text" name="name" placeholder="Your Name" class="w-full p-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/60" required>
                        <input type="email" name="email" placeholder="Your Email" class="w-full p-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/60" required>
                    </div>
                    <textarea name="message" placeholder="How can we help you?" class="w-full p-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/60" rows="4" required></textarea>
                    <div class="text-center">
                        <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:scale-105 transition-all">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 border-t border-white/20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center">
                <p class="text-white/60">© 2025 BSS Investment Group. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
@endsection