function shareholderDashboard() {
    return {
        // Navigation state
        activeLink: 'overview',
        sidebarOpen: false,
        sidebarCollapsed: false,
        sidebarSearch: '',
        
        // Modal states
        showLoanRequestModal: false,
        showChatModal: false,
        showMemberChatModal: false,
        showPerformanceModal: false,
        showDividendModal: false,
        showOpportunitiesModal: false,
        showProfileModal: false,
        showProfileViewModal: false,
        showLogoModal: false,
        showShareholderModal: false,
        showCalendarModal: false,
        showProfileDropdown: false,
        
        // Data states
        portfolioData: {
            shares: 1250,
            dividends: 125000,
            roi: 12.5,
            totalValue: 2450000,
            sharePrice: 1800
        },
        
        myFinancials: {
            savings: 1500000,
            loan: 500000,
            balance: 1000000
        },
        
        myLoans: [],
        
        // Chat data
        chatMessages: [
            {
                sender: 'support',
                text: 'Hello! How can I help you today?',
                time: new Date().toLocaleTimeString()
            }
        ],
        chatInput: '',
        
        // Loan request data
        loanRequest: {
            amount: '',
            purpose: '',
            repayment_months: ''
        },
        
        // Reverse order states
        reverseOrderState: {
            projects: false,
            dividends: false,
            loans: false,
            members: false,
            activities: false,
            savings: false,
            investments: false,
            transactions: false
        },
        
        // Sample data
        investmentProjects: [
            {
                id: 1,
                name: 'Community Water Project',
                progress: 65,
                budget: 5000000,
                expected_roi: 15.0,
                roi: 12.5
            },
            {
                id: 2,
                name: 'Solar Power Initiative',
                progress: 45,
                budget: 8000000,
                expected_roi: 18.5,
                roi: 16.2
            },
            {
                id: 3,
                name: 'Agricultural Expansion',
                progress: 80,
                budget: 3500000,
                expected_roi: 12.0,
                roi: 14.8
            }
        ],
        
        dividendHistory: [
            {
                id: 1,
                period: 'Q4 2023',
                amount: 125000,
                rate: 10,
                status: 'paid'
            },
            {
                id: 2,
                period: 'Q3 2023',
                amount: 118000,
                rate: 9.5,
                status: 'paid'
            },
            {
                id: 3,
                period: 'Q2 2023',
                amount: 110000,
                rate: 9.0,
                status: 'paid'
            }
        ],
        
        allMembers: [],
        filteredMembers: [],
        memberSearch: '',
        memberRoleFilter: '',
        
        recentActivities: [
            {
                title: 'New member joined',
                description: 'Sarah Johnson became a shareholder',
                time: '2 hours ago',
                icon: 'fas fa-user-plus',
                bgColor: 'bg-green-500'
            },
            {
                title: 'Dividend payment processed',
                description: 'Q4 2023 dividends distributed',
                time: '5 hours ago',
                icon: 'fas fa-coins',
                bgColor: 'bg-blue-500'
            },
            {
                title: 'Investment milestone reached',
                description: 'Water project 65% complete',
                time: '1 day ago',
                icon: 'fas fa-chart-line',
                bgColor: 'bg-purple-500'
            }
        ],
        
        // Methods
        init() {
            this.generateSampleMembers();
            this.filteredMembers = this.allMembers;
        },
        
        generateSampleMembers() {
            const roles = ['client', 'shareholder', 'cashier', 'td', 'ceo'];
            const locations = ['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu'];
            const occupations = ['Teacher', 'Engineer', 'Doctor', 'Farmer', 'Business Owner'];
            
            for (let i = 1; i <= 82; i++) {
                this.allMembers.push({
                    member_id: `BSS${String(i).padStart(3, '0')}`,
                    full_name: `Member ${i}`,
                    email: `member${i}@example.com`,
                    location: locations[Math.floor(Math.random() * locations.length)],
                    occupation: occupations[Math.floor(Math.random() * occupations.length)],
                    savings: Math.floor(Math.random() * 5000000) + 100000,
                    loan: Math.floor(Math.random() * 1000000),
                    balance: 0,
                    role: roles[Math.floor(Math.random() * roles.length)]
                });
            }
            
            // Calculate balance
            this.allMembers.forEach(member => {
                member.balance = member.savings - member.loan;
            });
        },
        
        formatCurrency(amount) {
            return 'UGX ' + (amount || 0).toLocaleString();
        },
        
        toggleReverseOrder(type) {
            this.reverseOrderState[type] = !this.reverseOrderState[type];
        },
        
        getReversedData(data, type) {
            if (this.reverseOrderState[type]) {
                return [...data].reverse();
            }
            return data;
        },
        
        submitLoanRequest() {
            if (!this.loanRequest.amount || !this.loanRequest.purpose || !this.loanRequest.repayment_months) {
                alert('Please fill in all fields');
                return;
            }
            
            const newLoan = {
                id: Date.now(),
                loan_id: `LN-2024-${String(this.myLoans.length + 1).padStart(3, '0')}`,
                amount: parseInt(this.loanRequest.amount),
                purpose: this.loanRequest.purpose,
                repayment_months: parseFloat(this.loanRequest.repayment_months),
                status: 'pending',
                created_at: new Date().toISOString(),
                monthly_payment: Math.round(parseInt(this.loanRequest.amount) * 1.1 / parseFloat(this.loanRequest.repayment_months))
            };
            
            this.myLoans.push(newLoan);
            this.showLoanRequestModal = false;
            
            // Reset form
            this.loanRequest = {
                amount: '',
                purpose: '',
                repayment_months: ''
            };
            
            alert('Loan request submitted successfully!');
        },
        
        sendMessage() {
            if (!this.chatInput.trim()) return;
            
            this.chatMessages.push({
                sender: 'user',
                text: this.chatInput,
                time: new Date().toLocaleTimeString()
            });
            
            this.chatInput = '';
            
            // Auto-reply after 1 second
            setTimeout(() => {
                this.chatMessages.push({
                    sender: 'support',
                    text: 'Thank you for your message. Our team will get back to you shortly.',
                    time: new Date().toLocaleTimeString()
                });
            }, 1000);
        },
        
        sendQuickMessage(message) {
            this.chatMessages.push({
                sender: 'user',
                text: message,
                time: new Date().toLocaleTimeString()
            });
            
            setTimeout(() => {
                this.chatMessages.push({
                    sender: 'support',
                    text: 'I understand you need help with that. Let me connect you with the right specialist.',
                    time: new Date().toLocaleTimeString()
                });
            }, 1000);
        },
        
        filterMembers() {
            let filtered = this.allMembers;
            
            if (this.memberSearch) {
                filtered = filtered.filter(member => 
                    member.full_name.toLowerCase().includes(this.memberSearch.toLowerCase()) ||
                    member.email.toLowerCase().includes(this.memberSearch.toLowerCase()) ||
                    member.member_id.toLowerCase().includes(this.memberSearch.toLowerCase())
                );
            }
            
            if (this.memberRoleFilter) {
                filtered = filtered.filter(member => member.role === this.memberRoleFilter);
            }
            
            this.filteredMembers = filtered;
        },
        
        handleSidebarSearch(query) {
            // Simple search functionality for sidebar
            console.log('Searching for:', query);
        },
        
        clearSidebarSearch() {
            this.sidebarSearch = '';
        }
    };
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Any additional initialization can go here
});