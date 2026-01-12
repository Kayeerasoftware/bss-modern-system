<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSS Investment Group - Complete System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="bssSystem()">
    <!-- Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-university text-2xl"></i>
                    <h1 class="text-xl font-bold">BSS Investment Group</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <select x-model="currentRole" @change="switchRole()" class="bg-blue-800 text-white px-3 py-1 rounded">
                        <option value="client">Client View</option>
                        <option value="shareholder">Shareholder View</option>
                        <option value="cashier">Cashier View</option>
                        <option value="td">TD View</option>
                        <option value="ceo">CEO View</option>
                        <option value="admin">Admin View</option>
                    </select>
                    <a href="/charts" class="bg-purple-600 px-4 py-2 rounded hover:bg-purple-700">
                        <i class="fas fa-chart-line mr-2"></i>Analytics
                    </a>
                    <button @click="logout()" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Dashboard Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Savings</p>
                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(dashboardData.totalSavings)"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-hand-holding-usd text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Active Loans</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(dashboardData.totalLoans)"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Members</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="dashboardData.members?.length || 0"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-project-diagram text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Active Projects</p>
                        <p class="text-2xl font-bold text-orange-600" x-text="dashboardData.projects?.length || 0"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-based Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Members Management -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Members</h3>
                    <button @click="showAddMember = true" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Add Member
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="member in dashboardData.members?.slice(0, 5)" :key="member.member_id">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <p class="font-medium" x-text="member.full_name"></p>
                                <p class="text-sm text-gray-600" x-text="member.email"></p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-green-600" x-text="formatCurrency(member.savings)"></p>
                                <p class="text-sm text-gray-600" x-text="member.role"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Loan Management -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Loan Applications</h3>
                    <button @click="showApplyLoan = true" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Apply for Loan
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="loan in dashboardData.pending_loans?.slice(0, 5)" :key="loan.loan_id">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <p class="font-medium" x-text="loan.member_id"></p>
                                <p class="text-sm text-gray-600" x-text="loan.purpose"></p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-blue-600" x-text="formatCurrency(loan.amount)"></p>
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800" x-text="loan.status"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Projects -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Projects</h3>
                    <button @click="showAddProject = true" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        <i class="fas fa-plus mr-2"></i>New Project
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="project in dashboardData.projects?.slice(0, 3)" :key="project.project_id">
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="flex justify-between items-center mb-2">
                                <p class="font-medium" x-text="project.name"></p>
                                <span class="text-sm text-gray-600" x-text="project.progress + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2" x-text="formatCurrency(project.budget)"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Recent Transactions</h3>
                    <button @click="showAddTransaction = true" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>New Transaction
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="transaction in dashboardData.recent_transactions?.slice(0, 5)" :key="transaction.transaction_id">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <p class="font-medium" x-text="transaction.member_id"></p>
                                <p class="text-sm text-gray-600" x-text="transaction.type"></p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium" :class="transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(transaction.amount)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Member Modal -->
    <div x-show="showAddMember" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Add New Member</h3>
            <form @submit.prevent="addMember()">
                <div class="space-y-4">
                    <input type="text" x-model="newMember.full_name" placeholder="Full Name" class="w-full p-3 border rounded" required>
                    <input type="email" x-model="newMember.email" placeholder="Email" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="newMember.contact" placeholder="Phone Number" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="newMember.location" placeholder="Location" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="newMember.occupation" placeholder="Occupation" class="w-full p-3 border rounded" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddMember = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Apply Loan Modal -->
    <div x-show="showApplyLoan" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Apply for Loan</h3>
            <form @submit.prevent="applyLoan()">
                <div class="space-y-4">
                    <input type="number" x-model="newLoan.amount" placeholder="Loan Amount" class="w-full p-3 border rounded" required>
                    <input type="text" x-model="newLoan.purpose" placeholder="Purpose" class="w-full p-3 border rounded" required>
                    <input type="number" x-model="newLoan.repayment_months" placeholder="Repayment Months" class="w-full p-3 border rounded" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showApplyLoan = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bssSystem() {
            return {
                currentRole: 'client',
                dashboardData: {},
                showAddMember: false,
                showApplyLoan: false,
                showAddProject: false,
                showAddTransaction: false,
                newMember: {},
                newLoan: {},
                
                init() {
                    this.loadDashboardData();
                },
                
                async loadDashboardData() {
                    try {
                        const response = await fetch('/api/dashboard-data?role=' + this.currentRole);
                        this.dashboardData = await response.json();
                    } catch (error) {
                        console.error('Error loading dashboard data:', error);
                        // Fallback data
                        this.dashboardData = {
                            totalSavings: 5000000,
                            totalLoans: 2000000,
                            members: [
                                {member_id: 'BSS001', full_name: 'John Doe', email: 'john@bss.com', savings: 500000, role: 'client'},
                                {member_id: 'BSS002', full_name: 'Jane Smith', email: 'jane@bss.com', savings: 750000, role: 'shareholder'}
                            ],
                            projects: [
                                {project_id: 'PRJ001', name: 'Community Water Project', budget: 5000000, progress: 65},
                                {project_id: 'PRJ002', name: 'Education Support', budget: 3000000, progress: 100}
                            ],
                            pending_loans: [
                                {loan_id: 'LOAN001', member_id: 'BSS001', amount: 200000, purpose: 'Business expansion', status: 'pending'}
                            ],
                            recent_transactions: [
                                {transaction_id: 'TXN001', member_id: 'BSS001', amount: 100000, type: 'deposit'}
                            ]
                        };
                    }
                },
                
                switchRole() {
                    this.loadDashboardData();
                },
                
                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-UG', {
                        style: 'currency',
                        currency: 'UGX',
                        minimumFractionDigits: 0
                    }).format(amount || 0);
                },
                
                async addMember() {
                    try {
                        const response = await fetch('/api/members', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(this.newMember)
                        });
                        
                        if (response.ok) {
                            this.showAddMember = false;
                            this.newMember = {};
                            this.loadDashboardData();
                            alert('Member added successfully!');
                        }
                    } catch (error) {
                        console.error('Error adding member:', error);
                        alert('Error adding member. Please try again.');
                    }
                },
                
                async applyLoan() {
                    try {
                        const response = await fetch('/api/apply-loan', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(this.newLoan)
                        });
                        
                        if (response.ok) {
                            this.showApplyLoan = false;
                            this.newLoan = {};
                            this.loadDashboardData();
                            alert('Loan application submitted successfully!');
                        }
                    } catch (error) {
                        console.error('Error applying for loan:', error);
                        alert('Error submitting loan application. Please try again.');
                    }
                },
                
                logout() {
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = '/login';
                    }
                }
            }
        }
    </script>
</body>
</html>