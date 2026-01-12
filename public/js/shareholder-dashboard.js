// ==================== SHAREHOLDER DASHBOARD JAVASCRIPT ====================
// Organized and modular JavaScript for shareholder dashboard

// ==================== ALPINE.JS DATA FUNCTION ====================

function shareholderDashboard() {
    return {
        // UI State
        sidebarOpen: false,
        activeLink: 'overview',

        // Reverse Order State
        reverseOrderState: {
            projects: false,
            dividends: false,
            loans: false,
            activities: false,
            members: false,
            transactions: false,
            savings: false
        },

        // Toggle reverse order for a specific section
        toggleReverseOrder: function(section) {
            this.reverseOrderState[section] = !this.reverseOrderState[section];
            console.log(`${section} order reversed:`, this.reverseOrderState[section]);
        },

        // Get reversed data based on section
        getReversedData: function(array, section) {
            if (this.reverseOrderState[section]) {
                return array ? [...array].reverse() : [];
            }
            return array || [];
        },

        // Portfolio Data
        portfolioData: {
            shares: 1500,
            dividends: 125000,
            roi: 12.5,
            totalValue: 2450000,
            sharePrice: 1800
        },

        portfolioView: 'combined',
        roiView: 'expected',

        // Investment Projects
        investmentProjects: [
            { id: 1, name: 'Community Water Project', budget: 5000000, progress: 65, expected_roi: 15.0, roi: 12.5 },
            { id: 2, name: 'Solar Power Initiative', budget: 8000000, progress: 45, expected_roi: 18.0, roi: 10.2 },
            { id: 3, name: 'Agricultural Cooperative', budget: 3000000, progress: 80, expected_roi: 12.0, roi: 14.5 }
        ],

        // Portfolio History
        portfolioHistory: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            values: [2100000, 2150000, 2200000, 2180000, 2250000, 2300000, 2280000, 2350000, 2400000, 2380000, 2420000, 2450000],
            prices: [1600, 1620, 1650, 1640, 1680, 1700, 1690, 1720, 1750, 1740, 1760, 1800]
        },

        // Dividend History
        dividendHistory: [
            { id: 1, period: 'Q4 2023', amount: 125000, rate: 10, status: 'paid' },
            { id: 2, period: 'Q3 2023', amount: 118000, rate: 9.5, status: 'paid' },
            { id: 3, period: 'Q2 2023', amount: 112000, rate: 9.0, status: 'paid' },
            { id: 4, period: 'Q1 2023', amount: 108000, rate: 8.5, status: 'paid' }
        ],

        // My Financials
        myFinancials: {
            savings: 1500000,
            loan: 500000,
            balance: 1000000
        },

        // My Loans
        myLoans: [
            { id: 1, loan_id: 'LN-2024-001', amount: 500000, purpose: 'Business Expansion', repayment_months: 12, monthly_payment: 45000, status: 'approved', created_at: '2024-01-15' },
            { id: 2, loan_id: 'LN-2024-002', amount: 200000, purpose: 'Home Improvement', repayment_months: 6, monthly_payment: 35000, status: 'pending', created_at: '2024-02-10' }
        ],

        loanRequest: { amount: '', purpose: '', repayment_months: '' },
        showLoanRequestModal: false,

        // Members Data
        allMembers: [],
        filteredMembers: [],
        memberSearch: '',
        memberRoleFilter: '',
        currentPage: 1,
        totalPages: 5,

        // Member Charts
        memberChartType: 'activity',
        memberRoleChartType: 'donut',
        memberGrowthView: 'area',
        savingsChartType: 'horizontal',
        insightView: 'engagement',

        // Recent Activities
        recentActivities: [
            { title: 'New member registration', description: 'John Doe joined as shareholder', icon: 'fas fa-user-plus', bgColor: 'bg-green-500', time: '2 hours ago' },
            { title: 'Dividend payment processed', description: 'Q4 2023 dividends sent to 45 members', icon: 'fas fa-coins', bgColor: 'bg-yellow-500', time: '5 hours ago' },
            { title: 'New investment launched', description: 'Solar Power Initiative now accepting investments', icon: 'fas fa-rocket', bgColor: 'bg-purple-500', time: '1 day ago' },
            { title: 'Loan approved', description: 'Loan application LN-2024-001 approved', icon: 'fas fa-check-circle', bgColor: 'bg-blue-500', time: '2 days ago' }
        ],
        showActivityTimeline: false,

        // Chat State
        showChatModal: false,
        chatInput: '',
        chatMessages: [],

        showMemberChatModal: false,
        selectedMemberChat: null,
        memberChatInput: '',
        memberChatMessages: [],
        memberChatSearch: '',
        filteredMembersChat: [],
        chatFilter: 'all',
        isTyping: false,

        // Modals
        showProfileModal: false,
        showProfileViewModal: false,
        showPerformanceModal: false,
        showDividendModal: false,
        showOpportunitiesModal: false,

        profilePicture: '',

        // Insights Data
        performanceData: {},
        dividendAnnouncement: {},
        opportunities: [],

        // Computed
        get onlineMembersCount() { return Math.round(this.allMembers.length * 0.65) || 53; },
        get averageSavings() { return this.formatCurrency(this.filteredMembers.reduce((sum, m) => sum + (m.savings || 0), 0) / (this.filteredMembers.length || 1)); },
        get averageEngagementScore() { return Math.round((85 + 72 + 68 + 55) / 4); },

        // Methods
        formatCurrency: function(value) {
            if (!value) return 'UGX 0';
            return 'UGX ' + parseFloat(value).toLocaleString();
        },

        updatePortfolioChart: function(history) {
            if (window.portfolioChart) {
                const labels = history.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const values = this.portfolioView === 'value' ? history.values : [];
                const prices = this.portfolioView === 'price' ? history.prices : [];

                const datasets = [];
                if (this.portfolioView === 'value' || this.portfolioView === 'combined') {
                    datasets.push({
                        label: 'Portfolio Value (UGX)',
                        data: values.length ? values : [2100000, 2150000, 2200000, 2180000, 2250000, 2300000, 2280000, 2350000, 2400000, 2380000, 2420000, 2450000],
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    });
                }
                if (this.portfolioView === 'price' || this.portfolioView === 'combined') {
                    datasets.push({
                        label: 'Share Price (UGX)',
                        data: prices.length ? prices : [1600, 1620, 1650, 1640, 1680, 1700, 1690, 1720, 1750, 1740, 1760, 1800],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: this.portfolioView === 'combined' ? 'y1' : 'y'
                    });
                }

                window.portfolioChart.data = { labels, datasets };
                if (this.portfolioView === 'combined') {
                    window.portfolioChart.options.scales.y1 = { position: 'right', beginAtZero: false, ticks: { callback: function(value) { return 'UGX ' + (value/1000).toFixed(0) + 'K'; } } };
                }
                window.portfolioChart.update();
            }
        },

        updateRoiChart: function(projects) {
            if (window.roiChart) {
                const projectData = this.getReversedData(projects || this.investmentProjects, 'projects');
                window.roiChart.data = {
                    labels: projectData.map(p => p.name),
                    datasets: [{
                        label: this.roiView === 'expected' ? 'Expected ROI (%)' : 'Actual ROI (%)',
                        data: projectData.map(p => this.roiView === 'expected' ? p.expected_roi : p.roi),
                        backgroundColor: ['#10B981', '#F59E0B', '#10B981'].slice(0, projectData.length)
                    }]
                };
                window.roiChart.update();
            }
        },

        updateMemberChart: function(type) {
            this.memberChartType = type;
            if (window.memberChartInstance) {
                window.memberChartInstance.destroy();
            }
            initializeMemberChart(type);
        },

        updateMemberRoleChart: function(type) {
            this.memberRoleChartType = type;
            if (window.memberRoleChartInstance) { window.memberRoleChartInstance.destroy(); }
            initMemberRoleChart();
        },

        updateMemberGrowthChart: function(view) {
            this.memberGrowthView = view;
            if (window.memberGrowthChartInstance) { window.memberGrowthChartInstance.destroy(); }
            initMemberGrowthChart();
        },

        updateSavingsChart: function(type) {
            this.savingsChartType = type;
            if (window.savingsChartInstance) { window.savingsChartInstance.destroy(); }
            initSavingsChart();
        },

        updateInsightsChart: function() {
            // Re-render insights chart based on insightView
        },

        filterByRole: function(role) {
            if (role === 'all') {
                this.filteredMembers = this.allMembers;
            } else {
                this.filteredMembers = this.allMembers.filter(m => m.role === role);
            }
        },

        filterMembers: function() {
            let members = [...this.allMembers];
            if (this.memberSearch) {
                const search = this.memberSearch.toLowerCase();
                members = members.filter(m =>
                    m.full_name?.toLowerCase().includes(search) ||
                    m.email?.toLowerCase().includes(search) ||
                    m.member_id?.toLowerCase().includes(search)
                );
            }
            if (this.memberRoleFilter) {
                members = members.filter(m => m.role === this.memberRoleFilter);
            }
            this.filteredMembers = members;
        },

        sortMembers: function(field) {
            this.filteredMembers.sort((a, b) => {
                if (a[field] < b[field]) return -1;
                if (a[field] > b[field]) return 1;
                return 0;
            });
        },

        exportMembersData: function() {
            console.log('Exporting members data...');
        },

        refreshAllData: function() {
            console.log('Refreshing all data...');
        },

        viewMemberDetails: function(member) {
            console.log('Viewing member details:', member);
        },

        messageMember: function(member) {
            console.log('Messaging member:', member);
        },

        sendPaymentRequest: function(member) {
            console.log('Sending payment request to:', member);
        },

        submitLoanRequest: function() {
            console.log('Submitting loan request:', this.loanRequest);
            this.showLoanRequestModal = false;
            this.loanRequest = { amount: '', purpose: '', repayment_months: '' };
        },

        sendMessage: function() {
            if (this.chatInput.trim()) {
                this.chatMessages.push({
                    sender: 'user',
                    text: this.chatInput,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
                this.chatInput = '';
            }
        },

        sendQuickMessage: function(text) {
            this.chatMessages.push({
                sender: 'user',
                text: text,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });
        },

        filterMembersForChat: function() {
            let members = [...this.allMembers];
            if (this.memberChatSearch) {
                const search = this.memberChatSearch.toLowerCase();
                members = members.filter(m =>
                    m.full_name?.toLowerCase().includes(search)
                );
            }
            if (this.chatFilter === 'online') {
                members = members.slice(0, Math.round(members.length * 0.65));
            }
            this.filteredMembersChat = members;
        },

        selectMemberChat: function(member) {
            this.selectedMemberChat = member;
            this.memberChatMessages = [
                { sender: 'them', text: 'Hello! How can I help you today?', time: '10:30 AM' },
                { sender: 'me', text: 'Hi, I have a question about my investment.', time: '10:31 AM' },
                { sender: 'them', text: 'Sure, I\'d be happy to help. What would you like to know?', time: '10:32 AM' }
            ];
        },

        sendMemberMessage: function() {
            if (this.memberChatInput.trim()) {
                this.memberChatMessages.push({
                    sender: 'me',
                    text: this.memberChatInput,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
                this.memberChatInput = '';
            }
        },

        startVideoCall: function() {
            console.log('Starting video call...');
        },

        startVoiceCall: function() {
            console.log('Starting voice call...');
        },

        initInsightsData: function() {
            this.performanceData = { benchmark_comparison: 3.2, trend: 'up' };
            this.dividendAnnouncement = { payment_date: '2024-03-15', amount: 135000 };
            this.opportunities = [
                { id: 1, title: 'Renewable Energy Project', description: 'Solar power installation for rural communities', target_amount: 50000000, minimum_investment: 500000, expected_roi: 18.5, risk_level: 'medium', status: 'active', deadline: '2024-03-30' },
                { id: 2, title: 'Agricultural Expansion', description: 'Supporting small-scale farmers', target_amount: 30000000, minimum_investment: 300000, expected_roi: 15.0, risk_level: 'low', status: 'active', deadline: '2024-04-15' }
            ];
        },

        init: function() {
            // Initialize members data
            this.allMembers = [
                { member_id: 'BSS001', full_name: 'John Doe', email: 'john@example.com', location: 'Kampala', occupation: 'Business Owner', savings: 2500000, loan: 0, balance: 2500000, role: 'shareholder' },
                { member_id: 'BSS002', full_name: 'Jane Smith', email: 'jane@example.com', location: 'Wakiso', occupation: 'Teacher', savings: 1800000, loan: 500000, balance: 1300000, role: 'client' },
                { member_id: 'BSS003', full_name: 'Robert Johnson', email: 'robert@example.com', location: 'Entebbe', occupation: 'Farmer', savings: 850000, loan: 200000, balance: 650000, role: 'client' },
                { member_id: 'BSS004', full_name: 'Sarah Williams', email: 'sarah@example.com', location: 'Jinja', occupation: 'Nurse', savings: 1200000, loan: 0, balance: 1200000, role: 'shareholder' },
                { member_id: 'BSS005', full_name: 'Michael Brown', email: 'michael@example.com', location: 'Kampala', occupation: 'Trader', savings: 3500000, loan: 800000, balance: 2700000, role: 'cashier' },
                { member_id: 'BSS006', full_name: 'Emily Davis', email: 'emily@example.com', location: 'Mbarara', occupation: ' Accountant', savings: 2100000, loan: 300000, balance: 1800000, role: 'td' },
                { member_id: 'BSS007', full_name: 'David Wilson', email: 'david@example.com', location: 'Gulu', occupation: 'Teacher', savings: 650000, loan: 100000, balance: 550000, role: 'client' },
                { member_id: 'BSS008', full_name: 'Lisa Anderson', email: 'lisa@example.com', location: 'Kampala', occupation: 'Doctor', savings: 4500000, loan: 0, balance: 4500000, role: 'ceo' }
            ];
            this.filteredMembers = [...this.allMembers];
            this.filteredMembersChat = [...this.allMembers];

            // Initialize charts
            this.$nextTick(() => {
                initializeCharts();
                initAllMemberCharts();
                this.updatePortfolioChart(this.portfolioHistory);
                this.updateRoiChart(this.investmentProjects);
            });
        }
    };
}

// Make function globally available
window.shareholderDashboard = shareholderDashboard;

// ==================== GLOBAL CONFIGURATION ====================
window.ChartDefaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: { duration: 1000, easing: 'easeOutQuart' },
    plugins: {
        legend: {
            labels: {
                usePointStyle: true,
                padding: 20,
                font: { size: 12, family: "'Segoe UI', sans-serif" }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: { size: 14 },
            bodyFont: { size: 13 },
            padding: 12,
            cornerRadius: 8,
            displayColors: true
        }
    }
};

// ==================== CHART INITIALIZATION ====================

// Initialize Chart.js charts
function initializeCharts() {
    console.log('Initializing charts...');

    // Portfolio Chart
    const portfolioCtx = document.getElementById('portfolioChart')?.getContext('2d');
    if (portfolioCtx) {
        window.portfolioChart = new Chart(portfolioCtx, {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: { callback: function(value) { return 'UGX ' + (value/1000000).toFixed(1) + 'M'; } }
                    }
                }
            }
        });
    }

    // Allocation Chart
    const allocationCtx = document.getElementById('allocationChart')?.getContext('2d');
    if (allocationCtx) {
        window.allocationChart = new Chart(allocationCtx, {
            type: 'doughnut',
            data: { labels: [], datasets: [{ data: [], backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'] }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // Dividend Chart
    const dividendCtx = document.getElementById('dividendChart')?.getContext('2d');
    if (dividendCtx) {
        window.dividendChart = new Chart(dividendCtx, {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Dividend Amount', data: [], backgroundColor: '#10B981', borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    // ROI Chart
    const roiCtx = document.getElementById('roiChart')?.getContext('2d');
    if (roiCtx) {
        window.roiChart = new Chart(roiCtx, {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'ROI (%)', data: [], backgroundColor: ['#10B981', '#F59E0B', '#10B981'], borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
        });
    }

    // Member Activity Chart
    initializeMemberChart('activity');
}

function initializeMemberChart(type) {
    const ctx = document.getElementById('memberChart')?.getContext('2d');
    if (!ctx) return;

    if (window.memberChartInstance) {
        window.memberChartInstance.destroy();
    }

    let config = {};
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    if (type === 'activity') {
        config = {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Active Members', data: [45, 48, 52, 55, 58, 62, 65, 68, 72, 75, 78, 82], borderColor: '#8B5CF6', backgroundColor: 'rgba(139, 92, 246, 0.1)', tension: 0.4, fill: true },
                    { label: 'New Members', data: [3, 4, 5, 3, 4, 6, 5, 4, 6, 5, 4, 5], borderColor: '#10B981', backgroundColor: 'rgba(16, 185, 129, 0.1)', tension: 0.4, fill: true }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
        };
    } else if (type === 'growth') {
        config = {
            type: 'bar',
            data: { labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024'], datasets: [{ label: 'Total Members', data: [45, 55, 65, 75, 82], backgroundColor: '#8B5CF6', borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        };
    } else if (type === 'distribution') {
        config = {
            type: 'doughnut',
            data: { labels: ['Client', 'Shareholder', 'Cashier', 'TD', 'CEO'], datasets: [{ data: [35, 30, 15, 12, 8], backgroundColor: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        };
    }

    window.memberChartInstance = new Chart(ctx, config);
}

window.updateMemberChart = function(type) { initializeMemberChart(type); };

// ==================== APEXCHARTS FUNCTIONS ====================

// Member Role Chart
window.memberRoleChartType = 'donut';
window.memberRoleChartInstance = null;

window.initMemberRoleChart = function() {
    const chartElement = document.getElementById('memberRoleChart');
    if (!chartElement) return;

    const options = {
        series: [44, 55, 13, 43, 22],
        labels: ['Client', 'Shareholder', 'Cashier', 'TD', 'CEO'],
        chart: { type: window.memberRoleChartType, height: 300, fontFamily: 'Segoe UI, sans-serif', animations: { enabled: true, easing: 'easeinout', speed: 800 } },
        colors: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'],
        plotOptions: {
            pie: {
                donut: { size: '60%', labels: { show: true, total: { show: true, showAlways: true, label: 'Total', fontSize: '14px', fontWeight: 600, color: '#374151' } } }
            },
            bar: { borderRadius: 8, horizontal: false, columnWidth: '60%' }
        },
        dataLabels: { enabled: true, formatter: function(val) { return val.toFixed(1) + '%'; }, style: { fontSize: '12px', colors: ['#fff'] } },
        legend: { position: 'bottom', fontSize: '13px', markers: { size: 8, shape: 'circle' }, itemMargin: { horizontal: 10, vertical: 5 } },
        stroke: { show: true, width: 2, colors: ['#fff'] },
        tooltip: { enabled: true, y: { formatter: function(val) { return val + ' members'; } } }
    };

    window.memberRoleChartInstance = new ApexCharts(chartElement, options);
    window.memberRoleChartInstance.render();
};

window.updateMemberRoleChart = function(type) {
    window.memberRoleChartType = type;
    if (window.memberRoleChartInstance) { window.memberRoleChartInstance.destroy(); }
    initMemberRoleChart();
};

// Member Growth Chart
window.memberGrowthView = 'area';
window.memberGrowthChartInstance = null;

window.initMemberGrowthChart = function() {
    const chartElement = document.getElementById('memberGrowthChart');
    if (!chartElement) return;

    const options = {
        series: [{ name: 'Total Members', data: [45, 52, 61, 71, 82] }, { name: 'New Members', data: [3, 5, 7, 8, 11] }],
        chart: { type: window.memberGrowthView, height: 300, fontFamily: 'Segoe UI, sans-serif', animations: { enabled: true, easing: 'easeinout', speed: 800 }, zoom: { enabled: true, type: 'x', autoScaleYaxis: true } },
        colors: ['#8B5CF6', '#10B981'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: window.memberGrowthView === 'area' ? 3 : 0, dashArray: window.memberGrowthView === 'line' ? 5 : 0 },
        fill: { type: window.memberGrowthView === 'area' ? 'gradient' : 'solid', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] } },
        xaxis: { categories: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024'], labels: { style: { fontSize: '12px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { fontSize: '12px' }, formatter: function(val) { return Math.round(val); } } },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '13px', markers: { size: 8, shape: 'circle' } },
        grid: { borderColor: '#E5E7EB', strokeDashArray: 4, xaxis: { lines: { show: true } } },
        tooltip: { shared: true, intersect: false, y: { formatter: function(val) { return val + ' members'; } } }
    };

    window.memberGrowthChartInstance = new ApexCharts(chartElement, options);
    window.memberGrowthChartInstance.render();
};

window.updateMemberGrowthChart = function(view) {
    window.memberGrowthView = view;
    if (window.memberGrowthChartInstance) { window.memberGrowthChartInstance.destroy(); }
    initMemberGrowthChart();
};

// Savings Distribution Chart
window.savingsChartType = 'horizontal';
window.savingsChartInstance = null;

window.initSavingsChart = function() {
    const chartElement = document.getElementById('savingsDistributionChart');
    if (!chartElement) return;

    const options = {
        series: [{ name: 'Members', data: [25, 35, 15, 12, 8, 5] }],
        chart: { type: 'bar', height: 250, fontFamily: 'Segoe UI, sans-serif', toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
        plotOptions: { bar: { horizontal: window.savingsChartType === 'horizontal', borderRadius: 6, barHeight: '70%', distributed: true, dataLabels: { position: 'center' } } },
        colors: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4'],
        dataLabels: { enabled: true, formatter: function(val) { return val + '%'; }, style: { fontSize: '11px', colors: ['#fff'] } },
        xaxis: { categories: ['< 100K', '100K-500K', '500K-1M', '1M-5M', '5M-10M', '> 10M'], labels: { style: { fontSize: '11px' } } },
        yaxis: { labels: { style: { fontSize: '11px' } } },
        legend: { show: false },
        grid: { borderColor: '#E5E7EB', strokeDashArray: 4 },
        tooltip: { y: { formatter: function(val) { return val + ' members'; } } }
    };

    window.savingsChartInstance = new ApexCharts(chartElement, options);
    window.savingsChartInstance.render();
};

window.updateSavingsChart = function(type) {
    window.savingsChartType = type;
    if (window.savingsChartInstance) { window.savingsChartInstance.destroy(); }
    initSavingsChart();
};

// Activity Heatmap Chart
window.activityHeatmapInstance = null;

function generateHeatmapData(min, max) {
    return Array.from({ length: 4 }, () => Math.floor(Math.random() * (max - min + 1)) + min);
}

window.initActivityHeatmapChart = function() {
    const chartElement = document.getElementById('activityHeatmapChart');
    if (!chartElement) return;

    const options = {
        series: [
            { name: 'Jan', data: generateHeatmapData(30, 40) },
            { name: 'Feb', data: generateHeatmapData(35, 45) },
            { name: 'Mar', data: generateHeatmapData(40, 50) },
            { name: 'Apr', data: generateHeatmapData(45, 55) },
            { name: 'May', data: generateHeatmapData(50, 60) },
            { name: 'Jun', data: generateHeatmapData(55, 65) },
            { name: 'Jul', data: generateHeatmapData(60, 70) },
            { name: 'Aug', data: generateHeatmapData(55, 65) },
            { name: 'Sep', data: generateHeatmapData(50, 60) },
            { name: 'Oct', data: generateHeatmapData(65, 75) },
            { name: 'Nov', data: generateHeatmapData(70, 80) },
            { name: 'Dec', data: generateHeatmapData(75, 85) }
        ],
        chart: { height: 250, type: 'heatmap', fontFamily: 'Segoe UI, sans-serif', toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
        dataLabels: { enabled: true, style: { fontSize: '10px', colors: ['#fff'] } },
        colors: ['#8B5CF6'],
        xaxis: { type: 'category', categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], labels: { style: { fontSize: '11px' } } },
        yaxis: { labels: { style: { fontSize: '11px' } } },
        grid: { borderColor: '#E5E7EB' },
        tooltip: { y: { formatter: function(val) { return val + ' activities'; } } }
    };

    window.activityHeatmapInstance = new ApexCharts(chartElement, options);
    window.activityHeatmapInstance.render();
};

// Location Treemap Chart
window.locationChartInstance = null;

window.initLocationChart = function() {
    const chartElement = document.getElementById('locationChart');
    if (!chartElement) return;

    const options = {
        series: [{ data: [
            { x: 'Kampala', y: 35 }, { x: 'Wakiso', y: 20 }, { x: 'Entebbe', y: 12 },
            { x: 'Jinja', y: 8 }, { x: 'Mbarara', y: 5 }, { x: 'Gulu', y: 3 }, { x: 'Other', y: 7 }
        ] }],
        legend: { show: false },
        chart: { height: 250, type: 'treemap', fontFamily: 'Segoe UI, sans-serif', toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 'bold' }, formatter: function(text, op) { return [text, op.value + '%']; } },
        colors: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4', '#EC4899'],
        plotOptions: { treemap: { distributed: true, enableShades: false, borderRadius: 6 } },
        tooltip: { y: { formatter: function(val) { return val + ' members'; } } }
    };

    window.locationChartInstance = new ApexCharts(chartElement, options);
    window.locationChartInstance.render();
};

// Engagement Radar Chart
window.engagementRadarChartInstance = null;

window.initEngagementRadarChart = function() {
    const chartElement = document.getElementById('engagementRadarChart');
    if (!chartElement) return;

    const options = {
        series: [{ name: 'This Month', data: [85, 72, 90, 68, 78, 88, 92] }, { name: 'Last Month', data: [78, 65, 82, 60, 70, 80, 85] }],
        chart: { height: 280, type: 'radar', fontFamily: 'Segoe UI, sans-serif', toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
        colors: ['#8B5CF6', '#93C5FD'],
        stroke: { width: 2, curve: 'smooth' },
        fill: { opacity: 0.4, colors: ['#8B5CF6', '#93C5FD'] },
        markers: { size: 4, hover: { size: 6 } },
        xaxis: { categories: ['Transactions', 'Logins', 'Deposits', 'Loans', 'Dividends', 'Chat', 'Meetings'], labels: { show: true, style: { colors: ['#6B7280'], fontSize: '11px' } } },
        yaxis: { show: false, min: 0, max: 100 },
        legend: { show: false },
        grid: { show: true, borderColor: '#E5E7EB', strokeDashArray: 4 },
        tooltip: { shared: true, intersect: false, y: { formatter: function(val) { return val + '% engagement'; } } }
    };

    window.engagementRadarChartInstance = new ApexCharts(chartElement, options);
    window.engagementRadarChartInstance.render();
};

// ==================== INITIALIZATION FUNCTIONS ====================

// Initialize all ApexCharts
window.initAllMemberCharts = function() {
    setTimeout(() => {
        initMemberRoleChart();
        initMemberGrowthChart();
        initSavingsChart();
        initActivityHeatmapChart();
        initLocationChart();
        initEngagementRadarChart();
        console.log('All ApexCharts initialized');
    }, 100);
};

// ==================== REVERSE DATA FUNCTIONS ====================

// Reverse array helper function
function reverseArray(array) {
    if (!array || !Array.isArray(array)) return [];
    return array.slice().reverse();
}

// Reverse order state
window.reverseOrderState = {
    projects: false,
    dividends: false,
    loans: false,
    activities: false,
    members: false
};

// Toggle reverse order for a specific section
window.toggleReverseOrder = function(section) {
    window.reverseOrderState[section] = !window.reverseOrderState[section];

    // Trigger UI update based on section
    switch(section) {
        case 'projects':
            if (window.portfolioApp) {
                window.portfolioApp.$nextTick(() => {
                    window.updateRoiChart(window.portfolioApp.investmentProjects);
                });
            }
            break;
        case 'dividends':
            // Re-render dividend table if needed
            break;
        case 'loans':
            // Re-render loans table if needed
            break;
        case 'activities':
            // Re-render activities if needed
            break;
        case 'members':
            // Re-render members table if needed
            break;
    }

    console.log(`${section} order reversed:`, window.reverseOrderState[section]);
};

// Get reversed array based on section
window.getReversedData = function(array, section) {
    if (window.reverseOrderState[section]) {
        return reverseArray(array);
    }
    return array || [];
};

function updateDashboardData() {
    const timestamp = new Date().toLocaleTimeString();
    document.getElementById('lastUpdate')?.setAttribute('data-time', timestamp);

    // Simulate real-time updates for demo purposes
    const liveIndicator = document.getElementById('liveIndicator');
    if (liveIndicator) {
        liveIndicator.classList.add('pulse');
        setTimeout(() => liveIndicator.classList.remove('pulse'), 2000);
    }
}

// Simulated real-time data updates (for demo)
function simulateRealTimeUpdates() {
    setInterval(() => {
        const randomChange = (Math.random() - 0.5) * 2;
        const totalSavingsEl = document.getElementById('totalSavingsDisplay');
        if (totalSavingsEl) {
            const currentValue = parseFloat(totalSavingsEl.getAttribute('data-value') || '125000000');
            const newValue = Math.max(0, currentValue + randomChange * 10000);
            totalSavingsEl.setAttribute('data-value', newValue);
            totalSavingsEl.textContent = 'UGX ' + (newValue / 1000000).toFixed(2) + 'M';
        }
    }, 5000);
}

// ==================== ANIMATION UTILITIES ====================

function animateValue(elementId, start, end, duration, prefix = '', suffix = '') {
    const element = document.getElementById(elementId);
    if (!element) return;

    const startTime = performance.now();
    const range = end - start;

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeProgress = 1 - Math.pow(1 - progress, 3);
        const current = start + range * easeProgress;

        element.textContent = prefix + Math.floor(current).toLocaleString() + suffix;

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

function triggerCardAnimation() {
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// ==================== INITIALIZATION ON DOM READY ====================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Shareholder Dashboard initializing...');

    // Initialize charts
    initializeCharts();

    // Initialize ApexCharts
    initAllMemberCharts();

    // Start real-time updates simulation
    simulateRealTimeUpdates();

    // Trigger animations
    triggerCardAnimation();

    console.log('Shareholder Dashboard initialized successfully');
});
