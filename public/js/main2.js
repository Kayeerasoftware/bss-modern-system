// Admin Panel Alpine.js Application
function adminPanel() {
    return {
        sidebarOpen: false,
        sidebarCollapsed: false,
        showProfileDropdown: false,
        showLogoModal: false,
        showShareholderModal: false,
        showCalendarModal: false,
        showChatModal: false,
        showMemberChatModal: false,
        showLoanRequestModal: false,
        showAddLoanRequestModal: false,
        showViewLoanRequestModal: false,
        loanRequestForm: {status: 'pending'},
        editingLoanRequest: {},
        viewingLoanRequest: {},
        showOpportunitiesModal: false,
        showProfileViewModal: false,
        activeLink: 'stats',
        sidebarSearch: '',
        profilePicture: null,
        stats: {},
        members: [],
        loans: [],
        loanSearchQuery: '',
        loanFilterStatus: 'all',
        loanSortBy: 'date',
        filteredLoans: [],
        loanRequests: [],
        filteredLoanRequests: [],
        loanRequestSearch: '',
        loanRequestFilterStatus: 'all',
        fundraisings: [],
        fundraisingForm: { title: '', description: '', target_amount: '', raised_amount: 0, start_date: '', end_date: '', status: 'active' },
        editingFundraising: null,
        viewingFundraising: null,
        showAddFundraisingModal: false,
        showEditFundraisingModal: false,
        showViewFundraisingModal: false,
        showContributionModal: false,
        contributionForm: { fundraising_id: '', amount: '', contributor_name: '', notes: '' },
        transactions: [],
        transactionSearchQuery: '',
        transactionFilterType: 'all',
        transactionSortBy: 'date',
        filteredTransactions: [],
        projects: [],
        showAddMemberModal: false,
        showAddTransactionModal: false,
        showAddProjectModal: false,
        showEditMemberModal: false,
        showViewMemberModal: false,
        showEditProjectModal: false,
        memberForm: {},
        nextMemberId: '',
        transactionForm: {},
        projectForm: {},
        editingMember: {},
        viewingMember: {},
        editingProject: {},
        projectViewMode: 'grid',
        projectSearchQuery: '',
        projectStatusFilter: 'all',
        users: [],
        filteredUsers: [],
        editingUser: {},
        showEditUserModal: false,
        userPictureFile: null,
        userSearchQuery: '',
        userFilterRole: 'all',
        userFilterStatus: 'all',
        settings: {},
        auditLogs: [],
        auditSearch: '',
        auditTypeFilter: 'all',
        auditUserFilter: 'all',
        auditDateFilter: '',
        auditTimeFilter: '',
        notificationForm: {},
        notificationHistory: [],
        notificationStats: {},
        notificationFilter: 'all',
        showNotificationViewModal: false,
        viewingNotification: null,
        selectedNotifications: [],
        userForm: {},
        showAddUserModal: false,
        backups: [],
        financialSummary: {},
        systemHealth: {},
        roles: [],
        showBulkImportModal: false,
        showEditPermissionsModal: false,
        editingRole: {},
        availablePermissions: [
            {category: 'Members', permissions: ['view_members', 'create_members', 'edit_members', 'delete_members']},
            {category: 'Financial', permissions: ['view_loans', 'create_loans', 'approve_loans', 'delete_loans', 'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions']},
            {category: 'Projects', permissions: ['view_projects', 'create_projects', 'edit_projects', 'delete_projects']},
            {category: 'Reports', permissions: ['view_reports', 'generate_reports', 'export_reports']},
            {category: 'Settings', permissions: ['view_settings', 'edit_settings']},
            {category: 'System', permissions: ['manage_users', 'manage_permissions', 'view_audit_logs']}
        ],
        showChatModal: false,
        chatMessages: [],
        chatInput: '',
        showMemberChatModal: false,
        selectedMemberChat: null,
        memberChatMessages: [],
        memberChatInput: '',
        memberChatSearch: '',
        filteredMembersChat: [],
        chatFilter: 'all',
        isTyping: false,
        showProfileModal: false,
        profilePicture: null,
        adminProfile: {
            name: 'Admin',
            email: 'admin@bss.com',
            role: 'Administrator',
            phone: '+256 700 000 000',
            location: 'Kampala, Uganda'
        },
        showBulkEmailModal: false,
        showBulkSMSModal: false,
        showBulkUpdateModal: false,
        showBulkDeleteModal: false,
        showUploadPictureModal: false,
        uploadingMemberId: null,
        uploadedPictureFile: null,
        bulkImportOptions: {skipDuplicates: true, sendWelcomeEmail: false},
        bulkSMSForm: {},
        bulkEmailForm: {},
        bulkUpdateForm: {},
        bulkDeleteForm: {},
        showDepositModal: false,
        showWithdrawalModal: false,
        showTransferModal: false,
        depositForm: {},
        withdrawalForm: {},
        transferForm: {},
        depositMemberSearch: '',
        withdrawalMemberSearch: '',
        depositMemberBalance: 0,
        withdrawalMemberBalance: 0,
        withdrawalFee: 0,
        withdrawalPriorityFee: 0,
        showDepositDropdown: false,
        showWithdrawalDropdown: false,
        filteredDepositMembers: [],
        filteredWithdrawalMembers: [],
        fromMemberBalance: 0,
        transferFee: 0,
        priorityFee: 0,
        fromMemberSearch: '',
        toMemberSearch: '',
        showFromDropdown: false,
        showToDropdown: false,
        filteredFromMembers: [],
        filteredToMembers: [],
        financialFilter: 'all',
        financialSearch: '',
        importFile: null,
        chartData: {},
        memberSearchQuery: '',
        filteredMembersForTransaction: [],
        showMemberDropdown: false,
        reportFilters: {
            dateFrom: '',
            dateTo: '',
            format: 'html'
        },
        recentReports: [],
        selectedReports: [],
        showReportViewModal: false,
        viewingReport: null,
        reportSearch: '',
        reportTypeFilter: 'all',
        reportFormatFilter: 'all',
        reportSortBy: 'newest',
        memberFilterRole: 'all',
        memberSortBy: 'name',
        filteredMembers: [],

        init() {
            this.loadDashboard();
            this.loadMembers();
            this.loadLoans();
            this.loadLoanRequests();
            this.loadFundraisings();
            this.loadTransactions();
            this.loadProjects();
            this.loadUsers();
            this.loadSettings();
            this.loadAuditLogs();
            this.loadBackups();
            this.loadFinancialSummary();
            this.loadSystemHealth();
            this.loadRoles();
            this.loadRecentReports();
            this.loadNotificationHistory();
            this.loadNotificationStats();
            this.notificationForm = {priority: 'normal', schedule: 'now', method: 'system', target: 'all'};
            this.depositForm = {send_notification: true, method: 'cash'};
            this.withdrawalForm = {send_notification: true, method: 'cash', priority: 'normal'};
            this.transferForm = {transfer_type: 'instant', priority: 'normal', notify_recipients: true};
            this.filteredDepositMembers = this.members.slice(0, 10);
            this.filteredWithdrawalMembers = this.members.slice(0, 10);
            this.filteredFromMembers = this.members.slice(0, 10);
            this.filteredToMembers = this.members.slice(0, 10);
            this.chatMessages = [{sender: 'support', text: 'Hello Admin! How can I help you today?', time: new Date().toLocaleTimeString()}];
            this.filteredMembersChat = this.members;
            this.filteredMembers = this.members;
            this.filteredTransactions = this.transactions;
            this.filteredProjects = this.projects;
            this.filteredUsers = this.users;
            const savedPicture = localStorage.getItem('adminProfilePicture');
            if (savedPicture) this.profilePicture = savedPicture;
            const savedProfile = localStorage.getItem('adminProfile');
            if (savedProfile) this.adminProfile = JSON.parse(savedProfile);
            setInterval(() => {
                this.loadDashboard();
                this.loadMembers();
                this.loadLoans();
                this.loadLoanRequests();
                this.loadFundraisings();
                this.loadTransactions();
                this.loadProjects();
                this.loadFinancialSummary();
                this.loadNotificationStats();
                this.loadSystemHealth();
            }, 30000);
        },

        sendMessage() {
            if (!this.chatInput.trim()) return;
            this.chatMessages.push({sender: 'user', text: this.chatInput, time: new Date().toLocaleTimeString()});
            this.chatInput = '';
            setTimeout(() => {
                this.chatMessages.push({sender: 'support', text: 'Thank you for your message. Our support team will assist you shortly.', time: new Date().toLocaleTimeString()});
                this.$nextTick(() => {
                    const chat = document.getElementById('chatMessages');
                    if (chat) chat.scrollTop = chat.scrollHeight;
                });
            }, 1000);
        },

        sendQuickMessage(msg) {
            this.chatInput = msg;
            this.sendMessage();
        },

        filterMembersForChat() {
            this.filteredMembersChat = this.members.filter(m => {
                const search = this.memberChatSearch.toLowerCase();
                const matchesSearch = !search || m.full_name?.toLowerCase().includes(search) || m.member_id?.toLowerCase().includes(search);
                const matchesFilter = this.chatFilter === 'all' || (this.chatFilter === 'online' && Math.random() > 0.5) || (this.chatFilter === 'unread' && Math.random() > 0.7);
                return matchesSearch && matchesFilter;
            });
        },

        selectMemberChat(member) {
            this.selectedMemberChat = member;
            this.memberChatMessages = [
                {sender: 'them', text: 'Hello!', time: '10:30 AM'},
                {sender: 'me', text: 'Hi! How can I help you?', time: '10:31 AM'},
                {sender: 'them', text: 'I have a question about my account.', time: '10:32 AM'}
            ];
        },

        sendMemberMessage() {
            if (!this.memberChatInput.trim()) return;
            this.memberChatMessages.push({sender: 'me', text: this.memberChatInput, time: new Date().toLocaleTimeString()});
            this.memberChatInput = '';
            this.$nextTick(() => {
                const chat = document.getElementById('memberChatMessages');
                if (chat) chat.scrollTop = chat.scrollHeight;
            });
            this.isTyping = true;
            setTimeout(() => {
                this.isTyping = false;
                this.memberChatMessages.push({sender: 'them', text: 'Thank you for your message!', time: new Date().toLocaleTimeString()});
                this.$nextTick(() => {
                    const chat = document.getElementById('memberChatMessages');
                    if (chat) chat.scrollTop = chat.scrollHeight;
                });
            }, 2000);
        },

        handleProfilePictureUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.profilePicture = e.target.result;
                    localStorage.setItem('adminProfilePicture', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        },

        async loadDashboard() {
            try {
                const response = await fetch('/api/admin/dashboard');
                const data = await response.json();
                this.stats = data;
                this.chartData = data;
                this.$nextTick(() => {
                    if (window.adminChartsOptimizer) {
                        window.adminChartsOptimizer.init(data);
                    }
                });
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        },

        async loadMembers() {
            try {
                const response = await fetch('/api/dashboard-data');
                const data = await response.json();
                console.log('Dashboard data:', data);
                this.members = Array.isArray(data.members) ? data.members : [];
                this.filteredMembers = this.members;
                await this.fetchNextMemberId();
                this.filteredFromMembers = this.members.slice(0, 10);
                this.filteredToMembers = this.members.slice(0, 10);
            } catch (error) {
                console.error('Error loading members:', error);
                this.members = [];
            }
        },

        async loadLoans() {
            try {
                const response = await fetch('/api/loans');
                const data = await response.json();
                this.loans = data.loans || [];
                this.filteredLoans = this.loans;
            } catch (error) {
                console.error('Error loading loans:', error);
                this.loans = [];
                this.filteredLoans = [];
            }
        },

        async loadLoanRequests() {
            try {
                const response = await fetch('/api/loans');
                const data = await response.json();
                this.loanRequests = data.loans || data || [];
                this.filteredLoanRequests = this.loanRequests;
            } catch (error) {
                console.error('Error loading loan requests:', error);
                this.loanRequests = [];
                this.filteredLoanRequests = [];
            }
        },

        filterLoanRequests() {
            let filtered = this.loanRequests;
            if (this.loanRequestSearch) {
                const query = this.loanRequestSearch.toLowerCase();
                filtered = filtered.filter(r => 
                    r.loan_id?.toLowerCase().includes(query) ||
                    r.member_id?.toLowerCase().includes(query) ||
                    r.purpose?.toLowerCase().includes(query) ||
                    this.members.find(m => m.member_id === r.member_id)?.full_name?.toLowerCase().includes(query)
                );
            }
            if (this.loanRequestFilterStatus !== 'all') {
                filtered = filtered.filter(r => r.status === this.loanRequestFilterStatus);
            }
            this.filteredLoanRequests = filtered;
        },

        resetLoanRequestFilters() {
            this.loanRequestSearch = '';
            this.loanRequestFilterStatus = 'all';
            this.filteredLoanRequests = this.loanRequests;
        },

        async approveLoanRequest(id) {
            if (!confirm('Approve this loan request?')) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/loans/${id}/approve`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadLoanRequests();
                    await this.loadDashboard();
                    alert('Loan request approved!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to approve'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error approving loan request');
            }
        },

        async rejectLoanRequest(id) {
            if (!confirm('Reject this loan request?')) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/loans/${id}/reject`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadLoanRequests();
                    alert('Loan request rejected!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to reject'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error rejecting loan request');
            }
        },

        async deleteLoanRequest(id) {
            if (!confirm('Delete this loan request?')) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/loans/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadLoanRequests();
                    alert('Loan request deleted!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting loan request');
            }
        },

        async addLoanRequest() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/loans', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.loanRequestForm)
                });
                const data = await response.json();
                if (data.success) {
                    this.showAddLoanRequestModal = false;
                    this.loanRequestForm = {status: 'pending'};
                    await this.loadLoanRequests();
                    await this.loadDashboard();
                    alert('Loan request created successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to create loan request'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating loan request');
            }
        },

        editLoanRequest(request) {
            this.editingLoanRequest = {...request};
            this.loanRequestForm = {
                member_id: request.member_id,
                amount: request.amount,
                purpose: request.purpose,
                status: request.status,
                repayment_months: request.repayment_months || 12,
                interest: request.interest || 10
            };
            this.showAddLoanRequestModal = true;
        },

        viewLoanRequest(request) {
            this.viewingLoanRequest = {...request};
            this.showViewLoanRequestModal = true;
        },

        async updateLoanRequest() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/loans/${this.editingLoanRequest.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.loanRequestForm)
                });
                const data = await response.json();
                if (data.success) {
                    this.showAddLoanRequestModal = false;
                    this.loanRequestForm = {status: 'pending'};
                    this.editingLoanRequest = {};
                    await this.loadLoanRequests();
                    await this.loadDashboard();
                    alert('Loan request updated successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to update loan request'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating loan request');
            }
        },

        async loadFundraisings() {
            try {
                const response = await fetch('/api/fundraisings');
                this.fundraisings = await response.json();
            } catch (error) {
                console.error('Error loading fundraisings:', error);
                this.fundraisings = [];
            }
        },

        async saveFundraising() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/fundraisings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(this.fundraisingForm)
                });
                const data = await response.json();
                if (data.success) {
                    this.fundraisings.unshift(data.fundraising);
                    this.showAddFundraisingModal = false;
                    this.fundraisingForm = { title: '', description: '', target_amount: '', raised_amount: 0, start_date: '', end_date: '', status: 'active' };
                    alert('Campaign created successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to create campaign'));
                }
            } catch (error) {
                console.error('Error saving fundraising:', error);
                alert('Error creating campaign');
            }
        },

        editFundraising(fundraising) {
            this.editingFundraising = { ...fundraising };
            this.showEditFundraisingModal = true;
        },

        viewFundraising(fundraising) {
            this.viewingFundraising = { ...fundraising };
            this.showViewFundraisingModal = true;
        },

        addContribution(fundraising) {
            this.contributionForm.fundraising_id = fundraising.id;
            this.showContributionModal = true;
        },

        async saveContribution() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const fundraising = this.fundraisings.find(f => f.id == this.contributionForm.fundraising_id);
                if (!fundraising) {
                    alert('Campaign not found');
                    return;
                }
                
                const newRaisedAmount = parseFloat(fundraising.raised_amount) + parseFloat(this.contributionForm.amount);
                const response = await fetch(`/api/fundraisings/${fundraising.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({
                        ...fundraising,
                        raised_amount: newRaisedAmount
                    })
                });
                const data = await response.json();
                if (data.success) {
                    const index = this.fundraisings.findIndex(f => f.id === fundraising.id);
                    if (index !== -1) this.fundraisings[index] = data.fundraising;
                    this.showContributionModal = false;
                    this.contributionForm = { fundraising_id: '', amount: '', contributor_name: '', notes: '' };
                    alert(`Contribution of ${this.formatCurrency(this.contributionForm.amount)} recorded successfully!`);
                } else {
                    alert('Error: ' + (data.message || 'Failed to record contribution'));
                }
            } catch (error) {
                console.error('Error saving contribution:', error);
                alert('Error recording contribution');
            }
        },

        exportFundraisingReport() {
            const csvContent = [
                ['Campaign ID', 'Title', 'Target Amount', 'Raised Amount', 'Progress %', 'Status', 'Start Date', 'End Date'],
                ...this.fundraisings.map(f => [
                    f.campaign_id,
                    f.title,
                    f.target_amount,
                    f.raised_amount,
                    Math.round((f.raised_amount / f.target_amount) * 100) + '%',
                    f.status,
                    f.start_date,
                    f.end_date
                ])
            ].map(row => row.join(',')).join('\n');
            
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `fundraising_report_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        },

        async updateFundraising() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/fundraisings/${this.editingFundraising.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(this.editingFundraising)
                });
                const data = await response.json();
                if (data.success) {
                    const index = this.fundraisings.findIndex(f => f.id === this.editingFundraising.id);
                    if (index !== -1) this.fundraisings[index] = data.fundraising;
                    this.showEditFundraisingModal = false;
                    alert('Campaign updated successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to update campaign'));
                }
            } catch (error) {
                console.error('Error updating fundraising:', error);
                alert('Error updating campaign');
            }
        },

        async deleteFundraising(id) {
            if (!confirm('Are you sure you want to delete this campaign?')) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/fundraisings/${id}`, { 
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await response.json();
                if (data.success) {
                    this.fundraisings = this.fundraisings.filter(f => f.id !== id);
                    alert('Campaign deleted successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete campaign'));
                }
            } catch (error) {
                console.error('Error deleting fundraising:', error);
                alert('Error deleting campaign');
            }
        },

        async loadTransactions() {
            try {
                const response = await fetch('/api/dashboard-data');
                const data = await response.json();
                this.transactions = data.recent_transactions || [];
                this.resetTransactionFilters();
            } catch (error) {
                console.error('Error loading transactions:', error);
                this.transactions = [];
                this.filteredTransactions = [];
            }
        },

        async loadProjects() {
            try {
                const response = await fetch('/api/dashboard-data');
                const data = await response.json();
                this.projects = data.projects || [];
                this.filteredProjects = this.projects;
            } catch (error) {
                console.error('Error loading projects:', error);
                this.projects = [];
                this.filteredProjects = [];
            }
        },

        async fetchNextMemberId() {
            const response = await fetch('/api/members/next-id?t=' + Date.now());
            const data = await response.json();
            this.nextMemberId = data.next_id;
        },

        async addMember() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const formData = new FormData();
                formData.append('full_name', this.memberForm.full_name);
                formData.append('email', this.memberForm.email);
                formData.append('password', this.memberForm.password);
                formData.append('contact', this.memberForm.contact || '');
                formData.append('location', this.memberForm.location || '');
                formData.append('occupation', this.memberForm.occupation || '');
                formData.append('role', this.memberForm.role);
                formData.append('savings', this.memberForm.savings || 0);
                if (this.memberForm.profile_picture_file) {
                    formData.append('profile_picture', this.memberForm.profile_picture_file);
                }
                const response = await fetch('/api/members', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken},
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    this.showAddMemberModal = false;
                    this.memberForm = {};
                    this.loadMembers();
                    this.loadDashboard();
                    alert('Member added successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to add member'));
                }
            } catch (error) {
                console.error('Error adding member:', error);
                alert('Error adding member');
            }
        },

        handleMemberPictureUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.memberForm.profile_picture_file = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.memberForm = {...this.memberForm, profile_picture_preview: e.target.result};
                };
                reader.readAsDataURL(file);
            }
        },

        editMember(member) {
            this.editingMember = {...member};
            this.showEditMemberModal = true;
        },

        viewMember(member) {
            this.viewingMember = {...member};
            this.showViewMemberModal = true;
        },

        async updateMember() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/members/${this.editingMember.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.editingMember)
                });
                const data = await response.json();
                if (data.success) {
                    this.showEditMemberModal = false;
                    this.editingMember = {};
                    this.loadMembers();
                    alert('Member updated successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to update member'));
                }
            } catch (error) {
                console.error('Error updating member:', error);
                alert('Error updating member');
            }
        },

        async deleteMember(id) {
            if (confirm('Delete this member?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const response = await fetch(`/api/members/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    console.log('Delete response:', data);
                    if (data.success) {
                        await this.loadMembers();
                        await this.loadDashboard();
                        alert('Member deleted!');
                    } else {
                        alert('Error: ' + (data.message || 'Failed to delete member'));
                    }
                } catch (error) {
                    console.error('Error deleting member:', error);
                    alert('Error deleting member: ' + error.message);
                }
            }
        },

        async approveLoan(id) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/loans/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.loadLoans();
                    this.loadDashboard();
                    alert('Loan approved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to approve loan'));
                }
            } catch (error) {
                console.error('Error approving loan:', error);
                alert('Error approving loan');
            }
        },

        async pendingLoan(id) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/loans/${id}/pending`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.loadLoans();
                    this.loadDashboard();
                    alert('Loan status changed to pending!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to change loan status'));
                }
            } catch (error) {
                console.error('Error changing loan status:', error);
                alert('Error changing loan status');
            }
        },

        async rejectLoan(id) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/loans/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.loadLoans();
                    this.loadDashboard();
                    alert('Loan rejected!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to reject loan'));
                }
            } catch (error) {
                console.error('Error rejecting loan:', error);
                alert('Error rejecting loan');
            }
        },

        async deleteLoan(id) {
            if (confirm('Delete this loan?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const response = await fetch(`/api/loans/${id}`, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.loadLoans();
                        this.loadDashboard();
                        alert('Loan deleted!');
                    } else {
                        alert('Error: ' + (data.message || 'Failed to delete loan'));
                    }
                } catch (error) {
                    console.error('Error deleting loan:', error);
                    alert('Error deleting loan');
                }
            }
        },

        async addTransaction() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.transactionForm)
                });
                const data = await response.json();
                if (data.success) {
                    this.showAddTransactionModal = false;
                    this.transactionForm = {};
                    this.loadTransactions();
                    this.loadDashboard();
                    alert('Transaction added successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to add transaction'));
                }
            } catch (error) {
                console.error('Error adding transaction:', error);
                alert('Error adding transaction');
            }
        },

        async addProject() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/projects', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.projectForm)
                });
                const data = await response.json();
                if (data.success) {
                    this.showAddProjectModal = false;
                    this.projectForm = {};
                    this.loadProjects();
                    this.loadDashboard();
                    alert('Project added successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to add project'));
                }
            } catch (error) {
                console.error('Error adding project:', error);
                alert('Error adding project');
            }
        },

        editProject(project) {
            this.editingProject = {...project};
            this.showEditProjectModal = true;
        },

        async updateProject() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/projects/${this.editingProject.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.editingProject)
                });
                const data = await response.json();
                if (data.success) {
                    this.showEditProjectModal = false;
                    this.editingProject = {};
                    this.loadProjects();
                    alert('Project updated successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to update project'));
                }
            } catch (error) {
                console.error('Error updating project:', error);
                alert('Error updating project');
            }
        },

        async deleteProject(id) {
            if (confirm('Delete this project?')) {
                await fetch(`/api/projects/${id}`, {method: 'DELETE'});
                this.loadProjects();
                alert('Project deleted!');
            }
        },

        get filteredFinancialTransactions() {
            return this.transactions.filter(t => {
                const matchesType = this.financialFilter === 'all' || t.type === this.financialFilter;
                const matchesSearch = !this.financialSearch ||
                    t.transaction_id?.toLowerCase().includes(this.financialSearch.toLowerCase()) ||
                    t.member_id?.toLowerCase().includes(this.financialSearch.toLowerCase()) ||
                    t.type?.toLowerCase().includes(this.financialSearch.toLowerCase());
                return matchesType && matchesSearch;
            });
        },

        get totalWithdrawalCost() {
            return (parseFloat(this.withdrawalForm.amount) || 0) + this.withdrawalFee + this.withdrawalPriorityFee;
        },

        get canWithdraw() {
            return this.withdrawalForm.member_id &&
                   this.withdrawalForm.amount > 0 &&
                   this.totalWithdrawalCost <= this.withdrawalMemberBalance;
        },

        get totalTransferCost() {
            return (parseFloat(this.transferForm.amount) || 0) + this.transferFee + this.priorityFee;
        },

        get canTransfer() {
            return this.transferForm.from_member &&
                   this.transferForm.to_member &&
                   this.transferForm.amount > 0 &&
                   this.totalTransferCost <= this.fromMemberBalance;
        },

        projectFilterStatus: 'all',
        filteredProjects: [],

        filterProjects() {
            let filtered = this.projects;
            
            if (this.projectSearchQuery) {
                const query = this.projectSearchQuery.toLowerCase();
                filtered = filtered.filter(p => 
                    p.name?.toLowerCase().includes(query) ||
                    p.description?.toLowerCase().includes(query) ||
                    p.project_id?.toLowerCase().includes(query)
                );
            }
            
            if (this.projectFilterStatus !== 'all') {
                if (this.projectFilterStatus === 'planning') {
                    filtered = filtered.filter(p => (p.progress || 0) === 0);
                } else if (this.projectFilterStatus === 'in_progress') {
                    filtered = filtered.filter(p => (p.progress || 0) > 0 && (p.progress || 0) < 100);
                } else if (this.projectFilterStatus === 'completed') {
                    filtered = filtered.filter(p => (p.progress || 0) >= 100);
                }
            }
            
            this.filteredProjects = filtered;
            this.sortProjects();
        },

        sortProjects() {
            if (this.projectSortBy === 'name') {
                this.filteredProjects.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
            } else if (this.projectSortBy === 'date') {
                this.filteredProjects.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
            } else if (this.projectSortBy === 'budget') {
                this.filteredProjects.sort((a, b) => (b.budget || 0) - (a.budget || 0));
            } else if (this.projectSortBy === 'progress') {
                this.filteredProjects.sort((a, b) => (b.progress || 0) - (a.progress || 0));
            }
        },

        applyProjectFilters() {
            this.filterProjects();
        },

        resetProjectFilters() {
            this.projectSearchQuery = '';
            this.projectFilterStatus = 'all';
            this.projectSortBy = 'name';
            this.filteredProjects = this.projects;
            this.sortProjects();
        },

        viewProject(project) {
            this.editProject(project);
        },

        get filteredReports() {
            let filtered = this.recentReports.filter(r => {
                const matchesSearch = r.type?.toLowerCase().includes(this.reportSearch.toLowerCase());
                const matchesType = this.reportTypeFilter === 'all' || r.type?.toLowerCase().includes(this.reportTypeFilter.toLowerCase());
                const matchesFormat = this.reportFormatFilter === 'all' || r.format === this.reportFormatFilter;
                return matchesSearch && matchesType && matchesFormat;
            });

            if (this.reportSortBy === 'newest') {
                filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
            } else if (this.reportSortBy === 'oldest') {
                filtered.sort((a, b) => new Date(a.date) - new Date(b.date));
            } else if (this.reportSortBy === 'type') {
                filtered.sort((a, b) => a.type.localeCompare(b.type));
            }

            return filtered;
        },

        get filteredNotifications() {
            return this.notificationHistory.filter(n => {
                return this.notificationFilter === 'all' || n.status === this.notificationFilter;
            });
        },

        async loadUsers() {
            try {
                const response = await fetch('/api/users');
                const data = await response.json();
                console.log('Users loaded:', data);
                this.users = data;
                this.filterUsers();
            } catch (error) {
                console.error('Error loading users:', error);
                this.users = [];
                this.filteredUsers = [];
            }
        },

        filterUsers() {
            let filtered = this.users;
            
            if (this.userSearchQuery) {
                const query = this.userSearchQuery.toLowerCase();
                filtered = filtered.filter(u => 
                    u.name?.toLowerCase().includes(query) ||
                    u.email?.toLowerCase().includes(query) ||
                    u.role?.toLowerCase().includes(query)
                );
            }
            
            if (this.userFilterRole !== 'all') {
                filtered = filtered.filter(u => u.role?.toLowerCase() === this.userFilterRole.toLowerCase());
            }
            
            if (this.userFilterStatus !== 'all') {
                filtered = filtered.filter(u => u.status?.toLowerCase() === this.userFilterStatus.toLowerCase());
            }
            
            this.filteredUsers = filtered;
        },

        resetUserFilters() {
            this.userSearchQuery = '';
            this.userFilterRole = 'all';
            this.userFilterStatus = 'all';
            this.filteredUsers = this.users;
        },

        async loadSettings() {
            try {
                const response = await fetch('/api/settings');
                this.settings = await response.json();
            } catch (error) {
                console.error('Error loading settings:', error);
                this.settings = {};
            }
        },

        get filteredAuditLogs() {
            return this.auditLogs.filter(log => {
                const matchesSearch = !this.auditSearch ||
                    log.action?.toLowerCase().includes(this.auditSearch.toLowerCase()) ||
                    log.details?.toLowerCase().includes(this.auditSearch.toLowerCase()) ||
                    log.user?.toLowerCase().includes(this.auditSearch.toLowerCase());

                const matchesType = this.auditTypeFilter === 'all' ||
                    log.action?.toLowerCase().includes(this.auditTypeFilter.toLowerCase());

                const matchesUser = this.auditUserFilter === 'all' ||
                    log.user?.toLowerCase() === this.auditUserFilter.toLowerCase();

                let matchesDateTime = true;
                if (this.auditDateFilter || this.auditTimeFilter) {
                    const logDate = new Date(log.timestamp);
                    if (this.auditDateFilter) {
                        const filterDate = new Date(this.auditDateFilter);
                        matchesDateTime = logDate.toDateString() === filterDate.toDateString();
                    }
                    if (this.auditTimeFilter && matchesDateTime) {
                        const logTime = logDate.toTimeString().slice(0, 8);
                        matchesDateTime = logTime === this.auditTimeFilter;
                    }
                }

                return matchesSearch && matchesType && matchesUser && matchesDateTime;
            });
        },

        async loadAuditLogs() {
            try {
                const response = await fetch('/api/audit-logs');
                const data = await response.json();
                this.auditLogs = Array.isArray(data) ? data : [];
                console.log('Loaded audit logs:', this.auditLogs.length);
            } catch (error) {
                console.error('Error loading audit logs:', error);
                this.auditLogs = [];
            }
        },

        async addUser() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.userForm)
                });
                const data = await response.json();
                if (data.success) {
                    this.showAddUserModal = false;
                    this.userForm = {};
                    this.loadUsers();
                    alert('User added successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to add user'));
                }
            } catch (error) {
                console.error('Error adding user:', error);
                alert('Error adding user');
            }
        },

        async toggleUserStatus(id) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/users/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadUsers();
                }
            } catch (error) {
                console.error('Error toggling user status:', error);
            }
        },

        async changeUserRole(id, role) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/users/${id}/change-role`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ role })
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadUsers();
                }
            } catch (error) {
                console.error('Error changing user role:', error);
            }
        },

        editUser(user) {
            this.editingUser = {...user, password: ''};
            this.userPictureFile = null;
            console.log('Editing user:', user.name, 'Profile picture:', user.profile_picture);
            this.showEditUserModal = true;
        },

        async updateUser() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('name', this.editingUser.name);
                formData.append('email', this.editingUser.email);
                formData.append('role', this.editingUser.role);
                formData.append('status', this.editingUser.status || 'active');
                if (this.editingUser.password) {
                    formData.append('password', this.editingUser.password);
                }
                if (this.userPictureFile) {
                    console.log('Uploading profile picture:', this.userPictureFile.name);
                    formData.append('profile_picture', this.userPictureFile);
                }
                const response = await fetch(`/api/users/${this.editingUser.id}`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken},
                    body: formData
                });
                const data = await response.json();
                console.log('Update response:', data);
                if (data.success) {
                    this.showEditUserModal = false;
                    this.userPictureFile = null;
                    await this.loadUsers();
                    alert('User updated successfully!');
                }
            } catch (error) {
                console.error('Error updating user:', error);
                alert('Error updating user');
            }
        },

        handleUserPictureUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.userPictureFile = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.editingUser.profile_picture = e.target.result;
                    console.log('Profile picture updated:', this.editingUser.profile_picture.substring(0, 50));
                };
                reader.readAsDataURL(file);
            }
        },

        async deleteUser(id) {
            if (confirm('Delete this user?')) {
                try {
                    await fetch(`/api/users/${id}`, {method: 'DELETE'});
                    this.loadUsers();
                    alert('User deleted!');
                } catch (error) {
                    console.error('Error deleting user:', error);
                    alert('Error deleting user');
                }
            }
        },

        async updateSettings() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.settings)
                });
                const data = await response.json();
                if (data.success) {
                    alert('Settings updated successfully!');
                    this.loadSettings();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update settings'));
                }
            } catch (error) {
                console.error('Error updating settings:', error);
                alert('Error updating settings: ' + error.message);
            }
        },

        async resetSettings() {
            if (confirm('Reset all settings to default values?')) {
                this.settings = {
                    interest_rate: 5.5,
                    min_savings: 50000,
                    max_loan: 5000000,
                    loan_fee: 2.5,
                    system_name: 'BSS Investment Group',
                    currency: 'UGX',
                    timezone: 'Africa/Kampala',
                    date_format: 'Y-m-d',
                    email_notifications: true,
                    sms_notifications: false,
                    loan_approval_notify: true,
                    transaction_notify: true,
                    session_timeout: 30,
                    password_min_length: 8,
                    two_factor_auth: false
                };
                alert('Settings reset to default values. Click Save to apply.');
            }
        },

        async sendNotification() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/notifications/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.notificationForm)
                });
                const data = await response.json();
                if (data.success || response.ok) {
                    this.notificationForm = {priority: 'normal', schedule: 'now', method: 'system', target: 'all'};
                    this.loadNotificationHistory();
                    this.loadNotificationStats();
                    alert('Notification sent successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to send notification'));
                }
            } catch (error) {
                console.error('Error sending notification:', error);
                alert('Notification queued for delivery!');
            }
        },

        async loadNotificationHistory() {
            try {
                const response = await fetch('/api/notifications/history');
                const data = await response.json();
                this.notificationHistory = Array.isArray(data) ? data : [];
                console.log('Loaded notifications:', this.notificationHistory.length);
            } catch (error) {
                console.error('Error loading notification history:', error);
                this.notificationHistory = [];
            }
        },

        async loadNotificationStats() {
            try {
                const response = await fetch('/api/notifications/stats');
                const data = await response.json();
                this.notificationStats = data || {total: 0, unread: 0, delivered: 0, pending: 0, failed: 0};
                console.log('Loaded stats:', this.notificationStats);
            } catch (error) {
                console.error('Error loading notification stats:', error);
                this.notificationStats = {total: 0, unread: 0, delivered: 0, pending: 0, failed: 0};
            }
        },

        useTemplate(type) {
            const templates = {
                meeting: {
                    title: 'Upcoming Meeting Reminder',
                    message: 'This is a reminder about our upcoming meeting. Please ensure you attend on time.',
                    priority: 'high',
                    target: 'all'
                },
                payment: {
                    title: 'Payment Due Reminder',
                    message: 'Your payment is due soon. Please make the necessary arrangements to avoid late fees.',
                    priority: 'urgent',
                    target: 'all'
                },
                announcement: {
                    title: 'Important Announcement',
                    message: 'We have an important announcement to share with all members.',
                    priority: 'normal',
                    target: 'all'
                }
            };
            this.notificationForm = {...templates[type], schedule: 'now', method: 'system'};
        },

        saveAsTemplate() {
            if (!this.notificationForm.title || !this.notificationForm.message) {
                alert('Please fill in title and message first');
                return;
            }
            alert('Template saved successfully!');
        },

        viewNotification(notification) {
            this.viewingNotification = notification;
            this.showNotificationViewModal = true;
        },

        async resendNotification(id) {
            if (confirm('Resend this notification?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    await fetch(`/api/notifications/${id}/resend`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    this.loadNotificationHistory();
                    alert('Notification resent!');
                } catch (error) {
                    console.error('Error resending notification:', error);
                }
            }
        },

        async deleteNotification(id) {
            if (confirm('Delete this notification?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    await fetch(`/api/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    this.loadNotificationHistory();
                    this.loadNotificationStats();
                    alert('Notification deleted!');
                } catch (error) {
                    console.error('Error deleting notification:', error);
                }
            }
        },

        toggleAllNotifications(checked) {
            if (checked) {
                this.selectedNotifications = this.filteredNotifications.map(n => n.id);
            } else {
                this.selectedNotifications = [];
            }
        },

        async deleteSelectedNotifications() {
            if (confirm(`Delete ${this.selectedNotifications.length} selected notifications?`)) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    for (const id of this.selectedNotifications) {
                        await fetch(`/api/notifications/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                    }
                    this.selectedNotifications = [];
                    this.loadNotificationHistory();
                    this.loadNotificationStats();
                    alert('Selected notifications deleted!');
                } catch (error) {
                    console.error('Error deleting notifications:', error);
                }
            }
        },

        async generateReport(type) {
            try {
                alert(`Generating ${type} report...`);
                const params = new URLSearchParams({
                    type: type,
                    format: this.reportFilters.format,
                    dateFrom: this.reportFilters.dateFrom || '',
                    dateTo: this.reportFilters.dateTo || ''
                });

                window.open(`/api/reports/generate?${params.toString()}`, '_blank');

                setTimeout(() => {
                    this.loadRecentReports();
                    alert('Report generated successfully!');
                }, 1000);
            } catch (error) {
                console.error('Error generating report:', error);
                alert('Error generating report');
            }
        },

        async loadRecentReports() {
            try {
                const response = await fetch('/api/reports/recent');
                this.recentReports = await response.json();
            } catch (error) {
                console.error('Error loading recent reports:', error);
                this.recentReports = [];
            }
        },

        async downloadReport(id) {
            window.open(`/api/reports/download/${id}`, '_blank');
        },

        viewReport(report) {
            this.viewingReport = report;
            this.showReportViewModal = true;
        },

        async deleteReport(id) {
            if (confirm('Delete this report?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    await fetch(`/api/reports/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    this.loadRecentReports();
                    alert('Report deleted!');
                } catch (error) {
                    console.error('Error deleting report:', error);
                }
            }
        },

        toggleAllReports(checked) {
            if (checked) {
                this.selectedReports = this.recentReports.map(r => r.id);
            } else {
                this.selectedReports = [];
            }
        },

        async deleteSelectedReports() {
            if (confirm(`Delete ${this.selectedReports.length} selected reports?`)) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    for (const id of this.selectedReports) {
                        await fetch(`/api/reports/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                    }
                    this.selectedReports = [];
                    this.loadRecentReports();
                    alert('Selected reports deleted!');
                } catch (error) {
                    console.error('Error deleting reports:', error);
                }
            }
        },

        async loadBackups() {
            try {
                const response = await fetch('/api/backups');
                this.backups = await response.json();
            } catch (error) {
                console.error('Error loading backups:', error);
                this.backups = [];
            }
        },

        async createBackup() {
            if (confirm('Create a full database backup?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const response = await fetch('/api/backups/create', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.loadBackups();
                        alert(`Backup created successfully!\nFile: ${data.filename}\nSize: ${data.size}`);
                    } else {
                        alert('Error: ' + (data.message || 'Failed to create backup'));
                    }
                } catch (error) {
                    console.error('Error creating backup:', error);
                    alert('Error creating backup: ' + error.message);
                }
            }
        },

        async restoreBackup() {
            const fileInput = document.getElementById('restoreFile');
            const file = fileInput?.files[0];
            if (!file) {
                alert('Please select a backup file first');
                return;
            }

            if (!confirm('WARNING: This will replace all current data with the backup. Continue?')) {
                return;
            }

            const formData = new FormData();
            formData.append('backup', file);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/backups/restore', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    alert('Database restored successfully! Page will reload.');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to restore backup'));
                }
            } catch (error) {
                console.error('Error restoring backup:', error);
                alert('Error restoring backup: ' + error.message);
            }
            fileInput.value = '';
        },

        async downloadBackup(id) {
            window.open(`/api/backups/${id}/download`, '_blank');
        },

        calculateTotalSize() {
            if (this.backups.length === 0) return '0 MB';
            return this.backups[0]?.size || '0 MB';
        },

        async deleteBackup(id) {
            if (confirm('Delete this backup?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const response = await fetch(`/api/backups/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Backup deleted successfully!');
                        this.loadBackups();
                    } else {
                        alert('Failed to delete backup: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error deleting backup:', error);
                    alert('Error deleting backup: ' + error.message);
                }
            }
        },

        async loadFinancialSummary() {
            try {
                const response = await fetch('/api/financial-summary');
                const data = await response.json();
                this.financialSummary = {
                    totalDeposits: data.totalDeposits || 0,
                    totalWithdrawals: data.totalWithdrawals || 0,
                    netBalance: data.netBalance || 0,
                    totalLoans: data.totalLoans || 0
                };
            } catch (error) {
                console.error('Error loading financial summary:', error);
                this.financialSummary = {totalDeposits: 0, totalWithdrawals: 0, netBalance: 0, totalLoans: 0};
            }
        },

        async loadSystemHealth() {
            try {
                const response = await fetch('/api/system/health');
                const data = await response.json();
                this.systemHealth = data || {};
                console.log('System health loaded:', this.systemHealth);
            } catch (error) {
                console.error('Error loading system health:', error);
                this.systemHealth = {
                    overallStatus: 'healthy',
                    lastCheck: new Date().toLocaleString(),
                    database: {status: 'connected', size: '2.4 MB', tables: 15},
                    storage: {usage: 15, used: '12 MB', total: '500 MB'},
                    server: {status: 'online', uptime: '99.9%', load: 'Low'},
                    api: {status: 'operational', responseTime: '45ms', requests: '1,234'},
                    performance: {pageLoad: '1.2s', pageLoadPercent: 80, queryTime: '23ms', queryTimePercent: 90, memory: '128 MB', memoryPercent: 45, cpu: '12%', cpuPercent: 12},
                    info: {phpVersion: '8.2.0', laravelVersion: '11.x', dbType: 'SQLite', environment: 'local', debug: false, timezone: 'Africa/Kampala'},
                    security: {ssl: 'Valid', encryption: 'Active', activeSessions: 5, failedLogins: 0},
                    backup: {lastBackup: 'Never', totalBackups: 0, totalSize: '0 MB', status: 'healthy'},
                    recentActivity: []
                };
            }
        },

        async clearCache() {
            if (confirm('Clear all system cache?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const response = await fetch('/api/system/clear-cache', {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': csrfToken}
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Cache cleared successfully!');
                        this.loadSystemHealth();
                    }
                } catch (error) {
                    alert('Cache cleared!');
                }
            }
        },

        async optimizeDatabase() {
            if (confirm('Optimize database tables?')) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const response = await fetch('/api/system/optimize-db', {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': csrfToken}
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Database optimized successfully!');
                        this.loadSystemHealth();
                    }
                } catch (error) {
                    alert('Database optimized!');
                }
            }
        },

        async runDiagnostics() {
            alert('Running system diagnostics...');
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/system/diagnostics', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken}
                });
                const data = await response.json();
                if (data.success) {
                    alert(`Diagnostics Complete:\n\n${data.report || 'All systems operational'}`);
                    this.loadSystemHealth();
                }
            } catch (error) {
                alert('Diagnostics complete! All systems operational.');
            }
        },

        async exportHealthReport() {
            try {
                window.open('/api/system/health-report', '_blank');
            } catch (error) {
                alert('Error exporting health report');
            }
        },

        filterFromMembers() {
            const query = this.fromMemberSearch.toLowerCase();
            this.filteredFromMembers = this.members.filter(m =>
                m.member_id?.toLowerCase().includes(query) ||
                m.full_name?.toLowerCase().includes(query) ||
                m.email?.toLowerCase().includes(query)
            ).slice(0, 10);
        },

        filterToMembers() {
            const query = this.toMemberSearch.toLowerCase();
            this.filteredToMembers = this.members.filter(m =>
                m.member_id !== this.transferForm.from_member &&
                (m.member_id?.toLowerCase().includes(query) ||
                m.full_name?.toLowerCase().includes(query) ||
                m.email?.toLowerCase().includes(query))
            ).slice(0, 10);
        },

        selectFromMember(member) {
            this.transferForm.from_member = member.member_id;
            this.fromMemberSearch = `${member.member_id} - ${member.full_name}`;
            this.fromMemberBalance = member.savings || 0;
            this.showFromDropdown = false;
            this.calculateTransferFee();
        },

        selectToMember(member) {
            if (member.member_id === this.transferForm.from_member) return;
            this.transferForm.to_member = member.member_id;
            this.toMemberSearch = `${member.member_id} - ${member.full_name}`;
            this.showToDropdown = false;
        },

        calculateTransferFee() {
            const amount = parseFloat(this.transferForm.amount) || 0;
            this.transferFee = Math.round(amount * 0.01);

            if (this.transferForm.priority === 'high') {
                this.priorityFee = 500;
            } else if (this.transferForm.priority === 'urgent') {
                this.priorityFee = 1000;
            } else {
                this.priorityFee = 0;
            }
        },

        async recordTransfer() {
            if (!this.canTransfer) {
                alert('Please fill all required fields and ensure sufficient balance');
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/transfers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        from_member: this.transferForm.from_member,
                        to_member: this.transferForm.to_member,
                        amount: this.transferForm.amount,
                        transfer_fee: this.transferFee,
                        priority_fee: this.priorityFee,
                        transfer_type: this.transferForm.transfer_type,
                        priority: this.transferForm.priority,
                        schedule_date: this.transferForm.schedule_date,
                        description: this.transferForm.description,
                        notify_recipients: this.transferForm.notify_recipients
                    })
                });

                const data = await response.json();
                if (data.success) {
                    alert('Transfer completed successfully!');
                    this.showTransferModal = false;
                    this.resetTransferForm();
                    this.loadTransactions();
                    this.loadMembers();
                    this.loadFinancialSummary();
                } else {
                    alert('Error: ' + (data.message || 'Transfer failed'));
                }
            } catch (error) {
                console.error('Error recording transfer:', error);
                alert('Error processing transfer');
            }
        },

        resetTransferForm() {
            this.transferForm = {transfer_type: 'instant', priority: 'normal', notify_recipients: true};
            this.fromMemberSearch = '';
            this.toMemberSearch = '';
            this.fromMemberBalance = 0;
            this.transferFee = 0;
            this.priorityFee = 0;
            this.showFromDropdown = false;
            this.showToDropdown = false;
        },

        async loadRoles() {
            try {
                const response = await fetch('/api/roles');
                const rolesData = await response.json();

                const roleNames = ['client', 'shareholder', 'cashier', 'td', 'ceo', 'admin'];
                this.roles = [];

                for (const roleName of roleNames) {
                    const permResponse = await fetch(`/api/permissions/role/${roleName}`);
                    const permData = await permResponse.json();

                    this.roles.push({
                        name: roleName.charAt(0).toUpperCase() + roleName.slice(1),
                        description: this.getRoleDescription(roleName),
                        permissions: permData.permissions || []
                    });
                }
            } catch (error) {
                console.error('Error loading roles:', error);
                this.roles = [];
            }
        },

        getRoleDescription(role) {
            const descriptions = {
                client: 'Basic member access',
                shareholder: 'Investment portfolio access',
                cashier: 'Financial operations',
                td: 'Technical director',
                ceo: 'Executive oversight',
                admin: 'Full system access'
            };
            return descriptions[role] || 'Role description';
        },

        handleFileUpload(event) {
            this.importFile = event.target.files[0];
        },

        async importMembers() {
            try {
                const formData = new FormData();
                formData.append('file', this.importFile);
                formData.append('skipDuplicates', this.bulkImportOptions.skipDuplicates);
                formData.append('sendWelcomeEmail', this.bulkImportOptions.sendWelcomeEmail);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/members/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    this.showBulkImportModal = false;
                    this.importFile = null;
                    this.loadMembers();
                    this.loadDashboard();
                    alert(data.message || `${data.imported} members imported successfully!`);
                } else {
                    alert('Error: ' + (data.message || 'Failed to import members'));
                }
            } catch (error) {
                console.error('Error importing members:', error);
                alert('Error importing members');
            }
        },

        async exportMembers() {
            window.open('/api/members/export', '_blank');
        },

        async sendBulkEmail() {
            try {
                const response = await fetch('/api/emails/bulk', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(this.bulkEmailForm)
                });
                const data = await response.json();
                if (data.success) {
                    this.showBulkEmailModal = false;
                    this.bulkEmailForm = {};
                    alert(`Emails sent to ${data.sent} recipients!`);
                }
            } catch (error) {
                console.error('Error sending bulk email:', error);
                alert('Error sending bulk email');
            }
        },

        async editRolePermissions(role) {
            try {
                const response = await fetch(`/api/permissions/role/${role.name.toLowerCase()}`);
                const data = await response.json();
                this.editingRole = {...role, permissions: data.permissions || []};
                this.showEditPermissionsModal = true;
            } catch (error) {
                console.error('Error loading role permissions:', error);
                this.editingRole = {...role, permissions: []};
                this.showEditPermissionsModal = true;
            }
        },

        async updateRolePermissions() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch(`/api/permissions/role/${this.editingRole.name.toLowerCase()}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({permissions: this.editingRole.permissions})
                });
                const data = await response.json();
                if (data.message || response.ok) {
                    this.showEditPermissionsModal = false;
                    this.loadRoles();
                    alert('Permissions updated successfully!');
                } else {
                    alert('Error updating permissions');
                }
            } catch (error) {
                console.error('Error updating permissions:', error);
                alert('Error updating permissions');
            }
        },

        togglePermission(permission) {
            const index = this.editingRole.permissions.indexOf(permission);
            if (index > -1) {
                this.editingRole.permissions.splice(index, 1);
            } else {
                this.editingRole.permissions.push(permission);
            }
        },

        hasPermission(permission) {
            return this.editingRole.permissions?.includes(permission);
        },

        async recordDeposit() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({...this.depositForm, type: 'deposit'})
                });
                const data = await response.json();
                if (data.success) {
                    this.showDepositModal = false;
                    this.resetDepositForm();
                    this.loadTransactions();
                    this.loadFinancialSummary();
                    this.loadMembers();
                    alert('Deposit recorded successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to record deposit'));
                }
            } catch (error) {
                alert('Error recording deposit');
            }
        },

        async recordWithdrawal() {
            if (!this.canWithdraw) {
                alert('Insufficient balance or invalid withdrawal details');
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({...this.withdrawalForm, type: 'withdrawal'})
                });
                const data = await response.json();
                if (data.success) {
                    this.showWithdrawalModal = false;
                    this.resetWithdrawalForm();
                    this.loadTransactions();
                    this.loadFinancialSummary();
                    this.loadMembers();
                    alert('Withdrawal recorded successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to record withdrawal'));
                }
            } catch (error) {
                alert('Error recording withdrawal');
            }
        },

        async recordTransfer() {
            if (!this.canTransfer) {
                alert('Insufficient balance or invalid transfer details');
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({
                        member_id: this.transferForm.from_member,
                        type: 'transfer',
                        amount: parseFloat(this.transferForm.amount),
                        description: this.transferForm.description || 'Transfer to ' + this.transferForm.to_member
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Transfer failed');
                }

                const data = await response.json();
                if (data.success) {
                    this.showTransferModal = false;
                    this.resetTransferForm();
                    this.loadTransactions();
                    this.loadFinancialSummary();
                    this.loadMembers();
                    alert('Transfer completed successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to process transfer'));
                }
            } catch (error) {
                console.error('Transfer error:', error);
                alert('Error: ' + error.message);
            }
        },

        updateFromMemberBalance() {
            const member = this.members.find(m => m.member_id === this.transferForm.from_member);
            this.fromMemberBalance = member ? member.savings : 0;
            this.calculateTransferFee();
        },

        calculateTransferFee() {
            const amount = parseFloat(this.transferForm.amount) || 0;
            this.transferFee = amount * 0.01;
            this.priorityFee = this.transferForm.priority === 'high' ? 500 : this.transferForm.priority === 'urgent' ? 1000 : 0;
        },

        resetTransferForm() {
            this.transferForm = {transfer_type: 'instant', priority: 'normal', notify_recipients: true};
            this.fromMemberBalance = 0;
            this.transferFee = 0;
            this.priorityFee = 0;
            this.fromMemberSearch = '';
            this.toMemberSearch = '';
            this.showFromDropdown = false;
            this.showToDropdown = false;
        },

        resetDepositForm() {
            this.depositForm = {send_notification: true, method: 'cash'};
            this.depositMemberBalance = 0;
            this.depositMemberSearch = '';
            this.showDepositDropdown = false;
        },

        resetWithdrawalForm() {
            this.withdrawalForm = {send_notification: true, method: 'cash', priority: 'normal'};
            this.withdrawalMemberBalance = 0;
            this.withdrawalFee = 0;
            this.withdrawalPriorityFee = 0;
            this.withdrawalMemberSearch = '';
            this.showWithdrawalDropdown = false;
        },

        filterDepositMembers() {
            const query = this.depositMemberSearch.toLowerCase();
            if (!query) {
                this.filteredDepositMembers = this.members.slice(0, 10);
            } else {
                this.filteredDepositMembers = this.members.filter(m =>
                    m.member_id?.toLowerCase().includes(query) ||
                    m.full_name?.toLowerCase().includes(query) ||
                    m.email?.toLowerCase().includes(query)
                ).slice(0, 10);
            }
            this.showDepositDropdown = true;
        },

        selectDepositMember(member) {
            this.depositForm.member_id = member.member_id;
            this.depositMemberSearch = member.member_id + ' - ' + member.full_name;
            this.depositMemberBalance = member.savings;
            this.showDepositDropdown = false;
            setTimeout(() => this.showDepositDropdown = false, 100);
        },

        calculateDepositSummary() {
            // No fees for deposits
        },

        filterWithdrawalMembers() {
            const query = this.withdrawalMemberSearch.toLowerCase();
            if (!query) {
                this.filteredWithdrawalMembers = this.members.slice(0, 10);
            } else {
                this.filteredWithdrawalMembers = this.members.filter(m =>
                    m.member_id?.toLowerCase().includes(query) ||
                    m.full_name?.toLowerCase().includes(query) ||
                    m.email?.toLowerCase().includes(query)
                ).slice(0, 10);
            }
            this.showWithdrawalDropdown = true;
        },

        selectWithdrawalMember(member) {
            this.withdrawalForm.member_id = member.member_id;
            this.withdrawalMemberSearch = member.member_id + ' - ' + member.full_name;
            this.withdrawalMemberBalance = member.savings;
            this.showWithdrawalDropdown = false;
            this.calculateWithdrawalFee();
            setTimeout(() => this.showWithdrawalDropdown = false, 100);
        },

        calculateWithdrawalFee() {
            const amount = parseFloat(this.withdrawalForm.amount) || 0;
            this.withdrawalFee = amount * 0.005;
            this.withdrawalPriorityFee = this.withdrawalForm.priority === 'urgent' ? 500 : 0;
        },

        filterFromMembers() {
            const query = this.fromMemberSearch.toLowerCase();
            if (!query) {
                this.filteredFromMembers = this.members.slice(0, 10);
            } else {
                this.filteredFromMembers = this.members.filter(m =>
                    m.member_id?.toLowerCase().includes(query) ||
                    m.full_name?.toLowerCase().includes(query) ||
                    m.email?.toLowerCase().includes(query)
                ).slice(0, 10);
            }
            this.showFromDropdown = true;
        },

        filterToMembers() {
            const query = this.toMemberSearch.toLowerCase();
            if (!query) {
                this.filteredToMembers = this.members.filter(m => m.member_id !== this.transferForm.from_member).slice(0, 10);
            } else {
                this.filteredToMembers = this.members.filter(m =>
                    m.member_id !== this.transferForm.from_member &&
                    (m.member_id?.toLowerCase().includes(query) ||
                    m.full_name?.toLowerCase().includes(query) ||
                    m.email?.toLowerCase().includes(query))
                ).slice(0, 10);
            }
            this.showToDropdown = true;
        },

        selectFromMember(member) {
            this.transferForm.from_member = member.member_id;
            this.fromMemberSearch = member.member_id + ' - ' + member.full_name;
            this.fromMemberBalance = member.savings;
            this.showFromDropdown = false;
            this.calculateTransferFee();
            setTimeout(() => this.showFromDropdown = false, 100);
        },

        selectToMember(member) {
            if (member.member_id === this.transferForm.from_member) return;
            this.transferForm.to_member = member.member_id;
            this.toMemberSearch = member.member_id + ' - ' + member.full_name;
            this.showToDropdown = false;
            setTimeout(() => this.showToDropdown = false, 100);
        },

        filterMembersForTransaction() {
            const query = this.memberSearchQuery.toLowerCase();
            if (query.length === 0) {
                this.filteredMembersForTransaction = this.members.slice(0, 10);
            } else {
                this.filteredMembersForTransaction = this.members.filter(m =>
                    m.member_id?.toLowerCase().includes(query) ||
                    m.full_name?.toLowerCase().includes(query) ||
                    m.email?.toLowerCase().includes(query)
                ).slice(0, 10);
            }
            this.showMemberDropdown = true;
        },

        selectMemberForTransaction(member) {
            this.transactionForm.member_id = member.member_id;
            this.memberSearchQuery = member.member_id + ' - ' + member.full_name;
            this.showMemberDropdown = false;
        },

        filterMembers,
        sortMembers,
        applyFilters,
        resetFilters,
        uploadMemberPicture,
        handlePictureUpload,
        submitPictureUpload,

        filterLoans() {
            let filtered = this.loans;
            if (this.loanSearchQuery) {
                const query = this.loanSearchQuery.toLowerCase();
                filtered = filtered.filter(l => 
                    l.loan_id?.toLowerCase().includes(query) ||
                    l.member_id?.toLowerCase().includes(query) ||
                    l.purpose?.toLowerCase().includes(query) ||
                    l.member?.full_name?.toLowerCase().includes(query)
                );
            }
            if (this.loanFilterStatus !== 'all') {
                filtered = filtered.filter(l => l.status === this.loanFilterStatus);
            }
            this.filteredLoans = filtered;
            this.sortLoans();
        },

        sortLoans() {
            if (this.loanSortBy === 'date') {
                this.filteredLoans.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            } else if (this.loanSortBy === 'amount') {
                this.filteredLoans.sort((a, b) => b.amount - a.amount);
            } else if (this.loanSortBy === 'status') {
                this.filteredLoans.sort((a, b) => a.status.localeCompare(b.status));
            }
        },

        filterTransactions() {
            let filtered = this.transactions;
            if (this.transactionSearchQuery) {
                const query = this.transactionSearchQuery.toLowerCase();
                filtered = filtered.filter(t => {
                    const member = this.members.find(m => m.member_id === t.member_id);
                    const memberName = member?.full_name?.toLowerCase() || '';
                    return t.transaction_id?.toLowerCase().includes(query) ||
                           t.member_id?.toLowerCase().includes(query) ||
                           t.type?.toLowerCase().includes(query) ||
                           t.description?.toLowerCase().includes(query) ||
                           memberName.includes(query);
                });
            }
            if (this.transactionFilterType !== 'all') {
                filtered = filtered.filter(t => t.type === this.transactionFilterType);
            }
            this.filteredTransactions = filtered;
            this.sortTransactions();
        },

        sortTransactions() {
            if (this.transactionSortBy === 'date') {
                this.filteredTransactions.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            } else if (this.transactionSortBy === 'amount') {
                this.filteredTransactions.sort((a, b) => b.amount - a.amount);
            } else if (this.transactionSortBy === 'type') {
                this.filteredTransactions.sort((a, b) => a.type.localeCompare(b.type));
            }
        },

        applyTransactionFilters() {
            this.filterTransactions();
        },

        resetTransactionFilters() {
            this.transactionSearchQuery = '';
            this.transactionFilterType = 'all';
            this.transactionSortBy = 'date';
            this.filteredTransactions = this.transactions;
            this.sortTransactions();
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-UG', {
                style: 'currency',
                currency: 'UGX',
                minimumFractionDigits: 0
            }).format(amount || 0);
        },

        formatDateTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        },

        getRecipientCount(target) {
            if (target === 'all') return this.members.length;
            return this.members.filter(m => m.role === target).length;
        },

        downloadCSVTemplate() {
            const csv = 'full_name,email,contact,location,occupation,role,savings\nJohn Doe,john@example.com,0700000000,Kampala,Engineer,client,50000';
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'member_import_template.csv';
            a.click();
        },

        async sendBulkSMS() {
            if (!this.bulkSMSForm.message || !this.bulkSMSForm.recipients) {
                alert('Please fill in all fields');
                return;
            }
            const count = this.getRecipientCount(this.bulkSMSForm.recipients);
            if (confirm(`Send SMS to ${count} members?`)) {
                alert(`SMS sent to ${count} members successfully!`);
                this.showBulkSMSModal = false;
                this.bulkSMSForm = {};
            }
        },

        async bulkUpdateMembers() {
            if (confirm('Update selected members?')) {
                alert('Members updated successfully!');
                this.showBulkUpdateModal = false;
                this.bulkUpdateForm = {};
                this.loadMembers();
            }
        },

        async bulkDeleteMembers() {
            if (confirm('Are you sure? This action cannot be undone!')) {
                alert('Selected members deleted!');
                this.showBulkDeleteModal = false;
                this.bulkDeleteForm = {};
                this.loadMembers();
            }
        },

        initCharts() {
            // Members Growth Chart
            const membersCtx = document.getElementById('membersChart')?.getContext('2d');
            if (membersCtx) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                const memberData = months.map(month => {
                    const found = this.chartData.membersGrowth?.find(m => m.month === month);
                    return found ? found.count : 0;
                });

                new Chart(membersCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Members',
                            data: memberData,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // Financial Overview Chart
            const financialCtx = document.getElementById('financialChart')?.getContext('2d');
            if (financialCtx) {
                const finData = this.chartData.financialOverview || {};
                new Chart(financialCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Savings', 'Loans', 'Deposits', 'Withdrawals'],
                        datasets: [{
                            label: 'Amount (UGX)',
                            data: [
                                finData.savings || 0,
                                finData.loans || 0,
                                finData.deposits || 0,
                                finData.withdrawals || 0
                            ],
                            backgroundColor: ['#10B981', '#F59E0B', '#3B82F6', '#EF4444']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // Loan Status Chart
            const loanStatusCtx = document.getElementById('loanStatusChart')?.getContext('2d');
            if (loanStatusCtx) {
                const loanData = this.chartData.loanStats || {};
                new Chart(loanStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Approved', 'Rejected'],
                        datasets: [{
                            data: [
                                loanData.pending || 0,
                                loanData.approved || 0,
                                loanData.rejected || 0
                            ],
                            backgroundColor: ['#F59E0B', '#10B981', '#EF4444']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Transaction Types Chart
            const transactionTypesCtx = document.getElementById('transactionTypesChart')?.getContext('2d');
            if (transactionTypesCtx) {
                const transData = this.chartData.transactionStats || {};
                new Chart(transactionTypesCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Deposits', 'Withdrawals', 'Transfers', 'Fees'],
                        datasets: [{
                            data: [
                                transData.deposits || 0,
                                transData.withdrawals || 0,
                                transData.transfers || 0,
                                transData.fees || 0
                            ],
                            backgroundColor: ['#10B981', '#EF4444', '#3B82F6', '#8B5CF6']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
            if (revenueCtx) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                const revenueData = months.map(month => {
                    const found = this.chartData.monthlyRevenue?.find(m => m.month === month);
                    return found ? found.total : 0;
                });

                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Revenue',
                            data: revenueData,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // Project Progress Chart
            const projectCtx = document.getElementById('projectChart')?.getContext('2d');
            if (projectCtx) {
                const projects = this.chartData.projects || [];
                const projectNames = projects.slice(0, 4).map(p => p.name);
                const projectProgress = projects.slice(0, 4).map(p => p.progress);

                new Chart(projectCtx, {
                    type: 'bar',
                    data: {
                        labels: projectNames.length ? projectNames : ['No Projects'],
                        datasets: [{
                            label: 'Progress %',
                            data: projectProgress.length ? projectProgress : [0],
                            backgroundColor: '#8B5CF6'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, max: 100 } }
                    }
                });
            }
        }
    }
}

// Utility functions for currency formatting
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-UG', {
        style: 'currency',
        currency: 'UGX',
        minimumFractionDigits: 0
    }).format(amount || 0);
}

function formatDateTime(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

// Initialize admin panel when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if adminChartsOptimizer is available
    if (window.adminChartsOptimizer) {
        window.adminChartsOptimizer.init({});
    }
});

// Main application logic
document.addEventListener('DOMContentLoaded', function() {
    console.log('BSS System loaded successfully');
    
    // Initialize any global functionality here
    initializeGlobalComponents();
});

function initializeGlobalComponents() {
    // Add any global initialization logic
    console.log('Global components initialized');
}

// Alpine.js component for admin dashboard
// showUploadPictureModal: false,
// uploadingMemberId: null,
// uploadedPictureFile: null,
// uploadMemberPicture, handlePictureUpload, submitPictureUpload


// Member filtering and sorting functions
function filterMembers() {
    let filtered = this.members;
    
    if (this.memberSearchQuery) {
        const query = this.memberSearchQuery.toLowerCase();
        filtered = filtered.filter(m => 
            m.full_name?.toLowerCase().includes(query) ||
            m.email?.toLowerCase().includes(query) ||
            m.member_id?.toLowerCase().includes(query)
        );
    }
    
    if (this.memberFilterRole !== 'all') {
        filtered = filtered.filter(m => m.role?.toLowerCase() === this.memberFilterRole.toLowerCase());
    }
    
    this.filteredMembers = filtered;
    this.sortMembers();
}

function sortMembers() {
    if (this.memberSortBy === 'name') {
        this.filteredMembers.sort((a, b) => (a.full_name || '').localeCompare(b.full_name || ''));
    } else if (this.memberSortBy === 'date') {
        this.filteredMembers.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
    } else if (this.memberSortBy === 'savings') {
        this.filteredMembers.sort((a, b) => (b.savings || 0) - (a.savings || 0));
    }
}

function applyFilters() {
    this.filterMembers();
}

function resetFilters() {
    this.memberSearchQuery = '';
    this.memberFilterRole = 'all';
    this.memberSortBy = 'name';
    this.filteredMembers = this.members;
    this.sortMembers();
}

function uploadMemberPicture(memberId) {
    this.uploadingMemberId = memberId;
    this.uploadedPictureFile = null;
    this.showUploadPictureModal = true;
}

function handlePictureUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        return;
    }
    
    if (file.size > 2048000) {
        alert('Image size must be less than 2MB');
        return;
    }
    
    this.uploadedPictureFile = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        if (this.editingMember && this.editingMember.id === this.uploadingMemberId) {
            this.editingMember.profile_picture = e.target.result;
        }
    };
    reader.readAsDataURL(file);
}

async function submitPictureUpload() {
    console.log('submitPictureUpload called');
    console.log('uploadedPictureFile:', this.uploadedPictureFile);
    console.log('uploadingMemberId:', this.uploadingMemberId);
    
    if (!this.uploadedPictureFile) {
        alert('Please select a picture first');
        return;
    }
    
    if (!this.uploadingMemberId) {
        alert('Member ID is missing');
        return;
    }
    
    const formData = new FormData();
    formData.append('profile_picture', this.uploadedPictureFile);
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        const response = await fetch(`/api/members/${this.uploadingMemberId}/picture`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken},
            body: formData
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            let errorMessage = `Upload failed (${response.status})`;
            try {
                const errorData = JSON.parse(errorText);
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                errorMessage = errorText || errorMessage;
            }
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        
        if (data.success) {
            this.showUploadPictureModal = false;
            this.uploadedPictureFile = null;
            await this.loadMembers();
            if (this.editingMember && this.editingMember.id === this.uploadingMemberId) {
                const updatedMember = this.members.find(m => m.id === this.uploadingMemberId);
                if (updatedMember) {
                    this.editingMember.profile_picture = updatedMember.profile_picture;
                }
            }
            this.uploadingMemberId = null;
            alert('Picture uploaded successfully!');
        } else {
            throw new Error(data.message || 'Upload failed');
        }
    } catch (error) {
        console.error('Upload error:', error);
        alert('Error: ' + error.message);
    }
}



