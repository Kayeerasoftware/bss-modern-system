@extends('layouts.app')

@section('title', 'BSS Dashboard')

@section('content')
<div x-data="bssApp()" class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold text-gray-800">BSS Investment Group</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span x-text="currentUser?.name || currentUser?.full_name" class="text-gray-600"></span>
                    <button @click="logout()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-16 flex">
        <!-- Sidebar -->
        <div class="w-64 sidebar-gradient text-white min-h-screen">
            <div class="p-6">
                <div class="mb-8">
                    <h3 class="font-semibold" x-text="currentUser?.name || currentUser?.full_name"></h3>
                    <p class="text-sm opacity-75" x-text="(currentRole || 'member').toUpperCase()"></p>
                </div>
                <nav class="space-y-2">
                    <template x-for="tab in dashboardTabs" :key="tab.id">
                        <button @click="activeTab = tab.id"
                                :class="activeTab === tab.id ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10'"
                                class="w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center space-x-3">
                            <i :class="tab.icon" class="w-5"></i>
                            <span x-text="tab.name"></span>
                        </button>
                    </template>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Dashboard Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2" x-text="currentTabName + ' Dashboard'"></h1>
                    <p class="text-gray-600">Welcome back, <span x-text="currentUser?.name || currentUser?.full_name"></span></p>
                </div>

                <!-- Dashboard Content -->
                <div id="dashboardContent">
                    <!-- Loading state -->
                    <div x-show="loading" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                        <p class="text-gray-500 mt-2">Loading dashboard...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bssApp', () => ({
        currentUser: null,
        currentRole: 'member',
        dashboardData: {},
        activeTab: 'overview',
        loading: true,

        get currentTabName() {
            const tab = this.dashboardTabs.find(t => t.id === this.activeTab);
            return tab ? tab.name : 'Dashboard';
        },

        get dashboardTabs() {
            const tabs = {
                client: [
                    { id: 'overview', name: 'Overview', icon: 'fas fa-home' },
                    { id: 'loans', name: 'My Loans', icon: 'fas fa-money-bill-wave' },
                    { id: 'savings', name: 'Savings', icon: 'fas fa-piggy-bank' }
                ],
                member: [
                    { id: 'overview', name: 'Overview', icon: 'fas fa-home' },
                    { id: 'profile', name: 'Profile', icon: 'fas fa-user' }
                ],
                admin: [
                    { id: 'overview', name: 'Overview', icon: 'fas fa-chart-line' },
                    { id: 'members', name: 'Members', icon: 'fas fa-users' },
                    { id: 'loans', name: 'Loans', icon: 'fas fa-money-bill-wave' },
                    { id: 'projects', name: 'Projects', icon: 'fas fa-project-diagram' }
                ]
            };
            return tabs[this.currentRole] || tabs.member;
        },

        async init() {
            await this.loadDashboard();
            this.$watch('activeTab', () => this.renderDashboard());
        },

        async loadDashboard() {
            try {
                const response = await fetch(`/api/dashboard-data?role=${this.currentRole}`, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.dashboardData = await response.json();
                this.currentUser = { name: 'Demo User', role: this.currentRole };
                this.loading = false;
                this.renderDashboard();
            } catch (error) {
                console.error('Failed to load dashboard:', error);
                this.loading = false;
            }
        },

        renderDashboard() {
            const content = document.getElementById('dashboardContent');
            
            if (this.activeTab === 'overview') {
                content.innerHTML = this.renderOverview();
            } else if (this.activeTab === 'members') {
                content.innerHTML = this.renderMembers();
            } else if (this.activeTab === 'loans') {
                content.innerHTML = this.renderLoans();
            } else if (this.activeTab === 'projects') {
                content.innerHTML = this.renderProjects();
            } else {
                content.innerHTML = '<div class="text-center py-8"><p class="text-gray-500">Content for ' + this.activeTab + ' coming soon...</p></div>';
            }
        },

        renderOverview() {
            return `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Members</p>
                                <p class="text-2xl font-bold text-gray-900">${this.dashboardData.members || 0}</p>
                            </div>
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Savings</p>
                                <p class="text-2xl font-bold text-gray-900">UGX ${(this.dashboardData.total_savings || 0).toLocaleString()}</p>
                            </div>
                            <i class="fas fa-piggy-bank text-green-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Active Loans</p>
                                <p class="text-2xl font-bold text-gray-900">${this.dashboardData.active_loans || 0}</p>
                            </div>
                            <i class="fas fa-money-bill-wave text-yellow-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Projects</p>
                                <p class="text-2xl font-bold text-gray-900">${(this.dashboardData.projects || []).length}</p>
                            </div>
                            <i class="fas fa-project-diagram text-purple-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
                        <div class="space-y-3">
                            ${(this.dashboardData.recent_transactions || []).map(t => `
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium">${t.description}</p>
                                        <p class="text-sm text-gray-600">${t.member_id}</p>
                                    </div>
                                    <span class="font-semibold ${t.type === 'deposit' ? 'text-green-600' : 'text-red-600'}">
                                        ${t.type === 'deposit' ? '+' : '-'}UGX ${t.amount.toLocaleString()}
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Active Projects</h3>
                        <div class="space-y-3">
                            ${(this.dashboardData.projects || []).slice(0, 3).map(p => `
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium">${p.name}</h4>
                                        <span class="text-sm px-2 py-1 rounded-full ${p.status === 'completed' ? 'bg-green-100 text-green-800' : p.status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">${p.status}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${p.progress || 0}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">${p.progress || 0}% complete</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        },

        renderMembers() {
            return `
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Member Management</h3>
                    <p class="text-gray-600">Member management features coming soon...</p>
                </div>
            `;
        },

        renderLoans() {
            return `
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Loan Management</h3>
                    <div class="space-y-4">
                        ${(this.dashboardData.pending_loans || []).map(loan => `
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium">${loan.member_id}</h4>
                                        <p class="text-sm text-gray-600">${loan.purpose}</p>
                                        <p class="text-sm text-gray-600">Amount: UGX ${loan.amount.toLocaleString()}</p>
                                    </div>
                                    <div class="space-x-2">
                                        <button onclick="approveLoan('${loan.id}')" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                            Approve
                                        </button>
                                        <button class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        },

        renderProjects() {
            return `
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Project Management</h3>
                    <div class="grid gap-4">
                        ${(this.dashboardData.projects || []).map(project => `
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium">${project.name}</h4>
                                    <span class="text-sm px-2 py-1 rounded-full ${project.status === 'completed' ? 'bg-green-100 text-green-800' : project.status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">${project.status}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">${project.description}</p>
                                <p class="text-sm text-gray-600 mb-2">Budget: UGX ${project.budget.toLocaleString()}</p>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: ${project.progress || 0}%"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">${project.progress || 0}% complete</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        },

        async logout() {
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                window.location.href = '/';
            } catch (error) {
                console.error('Logout failed:', error);
            }
        }
    }));
});

async function approveLoan(loanId) {
    try {
        const response = await fetch(`/api/approve-loan/${loanId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Failed to approve loan:', error);
    }
}
</script>
@endsection