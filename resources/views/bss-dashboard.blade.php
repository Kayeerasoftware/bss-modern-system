@extends('layouts.app')

@section('title', 'BSS Investment Group Dashboard')

@section('content')
    <div class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen pt-16" x-data="dashboardApp()">
        <!-- Header for Dashboard Content -->
        <header class="bg-gradient-to-r from-blue-900 to-blue-700 text-white relative overflow-hidden py-6 text-center shadow-lg">
            <div class="relative z-10 container mx-auto px-6">
                <h1 class="text-4xl font-bold mb-2">BSS Investment Group</h1>
                <p class="text-lg opacity-90">Empowering Bunya Secondary School Alumni</p>
                <a href="{{ route('bio-data-form') }}" class="absolute top-6 right-6 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">
                    Bio Data Form
                </a>
            </div>
        </header>

        <!-- Dashboard Navigation Tabs (simplified) -->
        <nav class="bg-gray-900 dark:bg-gray-800 text-white p-4 flex flex-wrap justify-center sm:justify-start items-center gap-2 shadow-md">
            <button @click="currentRole = 'client'; showDashboard('client')" :class="{'bg-blue-600': currentRole === 'client', 'hover:bg-gray-700': currentRole !== 'client'}" class="nav-tab px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-user mr-2"></i>Client
            </button>
            <button @click="currentRole = 'shareholder'; showDashboard('shareholder')" :class="{'bg-blue-600': currentRole === 'shareholder', 'hover:bg-gray-700': currentRole !== 'shareholder'}" class="nav-tab px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-user mr-2"></i>Shareholder
            </button>
            <button @click="currentRole = 'cashier'; showDashboard('cashier')" :class="{'bg-blue-600': currentRole === 'cashier', 'hover:bg-gray-700': currentRole !== 'cashier'}" class="nav-tab px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-user mr-2"></i>Cashier
            </button>
            <button @click="currentRole = 'td'; showDashboard('td')" :class="{'bg-blue-600': currentRole === 'td', 'hover:bg-gray-700': currentRole !== 'td'}" class="nav-tab px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-user mr-2"></i>Technical Director
            </button>
            <button @click="currentRole = 'ceo'; showDashboard('ceo')" :class="{'bg-blue-600': currentRole === 'ceo', 'hover:bg-gray-700': currentRole !== 'ceo'}" class="nav-tab px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-user mr-2"></i>CEO & Chairperson
            </button>
        </nav>

        <!-- Dashboard Content -->
        <div class="dashboard max-w-6xl mx-auto my-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6">
            <div id="clientDashboard" x-show="currentRole === 'client'" class="fade-in">
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white"><i class="fas fa-users mr-2"></i>Client Dashboard</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-700 border-collapse rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="p-3 text-left">Date</th>
                                <th class="p-3 text-left">Registered Members</th>
                                <th class="p-3 text-left">Cashier</th>
                                <th class="p-3 text-left">Technical Director</th>
                                <th class="p-3 text-left">CEO & Chairperson</th>
                                <th class="p-3 text-left">Loan Available?</th>
                            </tr>
                        </thead>
                        <tbody id="clientTable" class="divide-y divide-gray-200 dark:divide-gray-600">
                            <template x-if="clientDashboardData">
                                <tr>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="new Date().toLocaleDateString()"></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="clientDashboardData.totalMembers || 0"></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100">MUSIBIKA LYDIA</td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100">MBULAKYALO MATHIAS</td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100">NKULEGA RAYMON</td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100">Yes</td>
                                </tr>
                            </template>
                            <template x-if="!clientDashboardData">
                                <tr><td colspan="6" class="p-3 text-center text-gray-500 dark:text-gray-400">Loading data...</td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="shareholderDashboard" x-show="currentRole === 'shareholder'" class="fade-in">
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white"><i class="fas fa-user mr-2"></i>Shareholder Dashboard</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-700 border-collapse rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="p-3 text-left">SN</th>
                                <th class="p-3 text-left">Full Name</th>
                                <th class="p-3 text-left">Member ID</th>
                                <th class="p-3 text-left">Savings (UGX)</th>
                                <th class="p-3 text-left">Current Loan (UGX)</th>
                                <th class="p-3 text-left">Location</th>
                                <th class="p-3 text-left">Occupation</th>
                                <th class="p-3 text-left">Contacts</th>
                            </tr>
                        </thead>
                        <tbody id="shareholderTable" class="divide-y divide-gray-200 dark:divide-gray-600">
                            <template x-for="(member, i) in membersData" :key="member.id">
                                <tr>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="i + 1"></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="member.full_name || member.name"></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="member.member_id"></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100">UGX <span x-text="(member.savings || 0).toLocaleString()"></span></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100">UGX <span x-text="(member.loan || 0).toLocaleString()"></span></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="member.location || ''"></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="member.occupation || ''"></td>
                                    <td class="p-3 text-gray-800 dark:text-gray-100" x-text="member.contact || member.phone || ''"></td>
                                </tr>
                            </template>
                            <template x-if="!membersData.length">
                                <tr><td colspan="8" class="p-3 text-center text-gray-500 dark:text-gray-400">No members data available.</td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cashier Dashboard -->
            <div id="cashierDashboard" x-show="currentRole === 'cashier'" class="fade-in">
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white"><i class="fas fa-cash-register mr-2"></i>Cashier Dashboard</h2>
                <p class="text-gray-600 dark:text-gray-300">Cashier-specific content will go here.</p>
            </div>

            <!-- Technical Director Dashboard -->
            <div id="tdDashboard" x-show="currentRole === 'td'" class="fade-in">
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white"><i class="fas fa-wrench mr-2"></i>Technical Director Dashboard</h2>
                <p class="text-gray-600 dark:text-gray-300">Technical Director-specific content will go here.</p>
            </div>

            <!-- CEO Dashboard -->
            <div id="ceoDashboard" x-show="currentRole === 'ceo'" class="fade-in">
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white"><i class="fas fa-user-tie mr-2"></i>CEO Dashboard</h2>
                <p class="text-gray-600 dark:text-gray-300">CEO-specific content will go here.</p>
            </div>
        </div>

        <!-- Toast Notification -->
        <div id="toast" class="toast hidden"></div>
    </div>

    <script>
        function dashboardApp() {
            return {
                currentRole: 'client',
                clientDashboardData: null,
                membersData: [],

                init() {
                    this.showDashboard(this.currentRole);
                },

                async showDashboard(role) {
                    this.currentRole = role;
                    if (role === 'client') {
                        await this.updateClientDashboard();
                    } else if (role === 'shareholder') {
                        await this.updateShareholderDashboard();
                    }
                    // Add logic for cashier, td, ceo dashboards here
                },

                async updateClientDashboard() {
                    try {
                        const response = await fetch('/api/dashboard-data');
                        const result = await response.json();
                        if (response.ok) {
                            this.clientDashboardData = result.data;
                        } else {
                            this.showToast(result.message || 'Error loading client dashboard data', 'error');
                        }
                    } catch (error) {
                        console.error('Error loading client dashboard data:', error);
                        this.showToast('Error loading client dashboard data', 'error');
                    }
                },

                async updateShareholderDashboard() {
                    try {
                        const response = await fetch('/api/members');
                        const result = await response.json();
                        if (response.ok) {
                            this.membersData = result.data.data || result.data;
                        } else {
                            this.showToast(result.message || 'Error loading members data', 'error');
                        }
                    } catch (error) {
                        console.error('Error loading members data:', error);
                        this.showToast('Error loading members data', 'error');
                    }
                },

                showToast(message, type = 'success') {
                    const toast = document.getElementById('toast');
                    toast.textContent = message;
                    toast.className = `toast ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} show`;
                    setTimeout(() => toast.className = 'toast hidden', 3000);
                },
            }
        }
    </script>
    <style>
        /* Custom styles for toast notifications */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 1rem;
            border-radius: 8px;
            color: white;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .toast.show {
            opacity: 1;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection
