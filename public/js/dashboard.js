// BSS Dashboard JavaScript

// Global variables
let userRole = null;
let userRoles = [];
let selectedRole = null;

// Initialize user role from data attribute
function initUserRole() {
    const body = document.body;
    userRole = body.dataset.userRole || null;
    userRoles = JSON.parse(body.dataset.userRoles || '[]');
}

// Role selection and navigation
function selectRole(role) {
    selectedRole = role;
    
    // Update navbar buttons
    document.querySelectorAll('.navbar-item').forEach(btn => {
        btn.classList.remove('selected');
    });
    document.querySelector(`button[onclick="selectRole('${role}')"]`).classList.add('selected');
    
    // Update status text
    const statusTexts = document.querySelectorAll('.role-status-text');
    statusTexts.forEach(text => {
        const btnRole = text.closest('.text-center').querySelector('button').getAttribute('onclick').match(/'([^']+)'/)[1];
        if (btnRole === role) {
            text.textContent = 'Selected Role';
            text.classList.remove('text-red-400', 'text-green-400');
            text.classList.add('text-blue-400');
        } else if (btnRole === userRole) {
            text.textContent = 'Active';
            text.classList.remove('text-blue-400', 'text-red-400');
            text.classList.add('text-green-400');
        } else {
            text.textContent = 'Not Active';
            text.classList.remove('text-blue-400', 'text-green-400');
            text.classList.add('text-red-400');
        }
    });
    
    const roleConfig = {
        'client': {
            name: 'Client',
            icon: 'fa-user-circle',
            colors: ['#3b82f6', '#2563eb'],
            features: ['Personal account management', 'View savings balance', 'Apply for loans', 'Track transactions', 'Investment portfolio', 'Financial statements']
        },
        'shareholder': {
            name: 'Shareholder',
            icon: 'fa-chart-line',
            colors: ['#10b981', '#059669'],
            features: ['Monitor shareholdings', 'Track dividend payments', 'Investment returns', 'Financial reports', 'Equity stake management', 'Portfolio analytics']
        },
        'cashier': {
            name: 'Cashier',
            icon: 'fa-cash-register',
            colors: ['#f59e0b', '#d97706'],
            features: ['Process transactions', 'Deposits & withdrawals', 'Register members', 'Daily cash operations', 'Transaction reports', 'Balance reconciliation']
        },
        'td': {
            name: 'Technical Director',
            icon: 'fa-project-diagram',
            colors: ['#8b5cf6', '#7c3aed'],
            features: ['Technical oversight', 'System infrastructure', 'Technical support', 'IT projects coordination', 'System security', 'Data management']
        },
        'ceo': {
            name: 'CEO & Chairperson',
            icon: 'fa-crown',
            colors: ['#ec4899', '#db2777'],
            features: ['Executive dashboard', 'Financial reports', 'Performance monitoring', 'Strategic decisions', 'Operations oversight', 'Leadership analytics']
        },
        'admin': {
            name: 'Admin',
            icon: 'fa-user-shield',
            colors: ['#dc2626', '#991b1b'],
            features: ['Full system access', 'User management', 'System configuration', 'Security settings', 'Database management', 'System logs']
        }
    };

    const config = roleConfig[role];
    const card = document.getElementById('roleCard');
    card.style.setProperty('--role-start', config.colors[0]);
    card.style.setProperty('--role-end', config.colors[1]);

    document.getElementById('roleDefault').classList.add('hidden');
    document.getElementById('roleSelected').classList.remove('hidden');
    document.getElementById('roleIcon').innerHTML = `<i class="fas ${config.icon}"></i>`;
    document.getElementById('roleTitle').textContent = config.name;

    const statusDiv = document.getElementById('roleStatus');
    const buttonDiv = document.getElementById('roleButton');
    
    if (userRoles.includes(role)) {
        statusDiv.innerHTML = '<button onclick="goToRole()" class="access-badge" style="background: linear-gradient(135deg, #10b981, #059669); color: white; cursor: pointer; border: none;"><i class="fas fa-check-circle"></i> Access Granted - GO!</button>';
        buttonDiv.classList.remove('hidden');
    } else {
        statusDiv.innerHTML = '<span class="access-badge" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;"><i class="fas fa-lock"></i> No Access</span>';
        buttonDiv.classList.add('hidden');
    }

    const featuresDiv = document.getElementById('roleFeatures');
    featuresDiv.innerHTML = config.features.map(f => 
        `<div class="feature-item" style="border-left-color: ${config.colors[0]}">
            <i class="fas fa-check-circle text-sm mr-2 mt-0.5" style="color: ${config.colors[0]}"></i>
            <span class="text-sm text-gray-700 font-medium">${f}</span>
        </div>`
    ).join('');
}

function goToRole() {
    if (selectedRole && userRoles.includes(selectedRole)) {
        // Switch role via API first
        fetch('/switch-role', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ role: selectedRole })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const roleRoutes = {
                    'client': '/client/dashboard',
                    'shareholder': '/shareholder/dashboard',
                    'cashier': '/cashier/dashboard',
                    'td': '/td/dashboard',
                    'ceo': '/ceo/dashboard',
                    'admin': '/admin/dashboard'
                };
                window.location.href = roleRoutes[selectedRole] || '/dashboard';
            } else {
                showToast('Failed to switch role', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error switching role', 'error');
        });
    }
}

// Transition effects
const transitions = ['fade', 'slide', 'zoom', 'flip'];

function applyTransition(element, type) {
    element.style.transition = 'all 1s ease-in-out';
    if (type === 'fade') {
        element.style.opacity = '1';
    } else if (type === 'slide') {
        element.style.transform = 'translateX(0)';
        element.style.opacity = '1';
    } else if (type === 'zoom') {
        element.style.transform = 'scale(1)';
        element.style.opacity = '1';
    } else if (type === 'flip') {
        element.style.transform = 'rotateY(0deg)';
        element.style.opacity = '1';
    }
}

function resetTransition(element, type) {
    if (type === 'slide') {
        element.style.transform = 'translateX(100%)';
    } else if (type === 'zoom') {
        element.style.transform = 'scale(0.8)';
    } else if (type === 'flip') {
        element.style.transform = 'rotateY(90deg)';
    }
    element.style.opacity = '0';
}

// Project Photos Slideshow
function initProjectSlideshow() {
    const projectSlides = document.querySelectorAll('.project-slide');
    const projectGrid = document.getElementById('projectGrid');
    if (projectSlides.length > 0) {
        setTimeout(() => {
            projectGrid.style.display = 'none';
            let projectIndex = 0;
            projectSlides[0].style.opacity = '1';

            setInterval(() => {
                const currentTransition = transitions[projectIndex % transitions.length];
                resetTransition(projectSlides[projectIndex], currentTransition);
                projectIndex = (projectIndex + 1) % projectSlides.length;
                const nextTransition = transitions[projectIndex % transitions.length];
                applyTransition(projectSlides[projectIndex], nextTransition);
            }, 5000);
        }, 6000);
    }
}

// Meeting Photos Slideshow
function initMeetingSlideshow() {
    const meetingSlides = document.querySelectorAll('.meeting-slide');
    const meetingGrid = document.getElementById('meetingGrid');
    if (meetingSlides.length > 0) {
        setTimeout(() => {
            meetingGrid.style.display = 'none';
            let meetingIndex = 0;
            meetingSlides[0].style.opacity = '1';

            setInterval(() => {
                const currentTransition = transitions[meetingIndex % transitions.length];
                resetTransition(meetingSlides[meetingIndex], currentTransition);
                meetingIndex = (meetingIndex + 1) % meetingSlides.length;
                const nextTransition = transitions[meetingIndex % transitions.length];
                applyTransition(meetingSlides[meetingIndex], nextTransition);
            }, 5000);
        }, 8500);
    }
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast show ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// Image slider
let slideIndex = 0;
function showSlides() {
    const slides = document.querySelector('.slides');
    if (slides) {
        slideIndex++;
        if (slideIndex >= slides.children.length) slideIndex = 0;
        slides.style.transform = `translateX(-${slideIndex * 100}%)`;
    }
}

// Dashboard navigation
function showDashboard(role) {
    window.location.href = `/dashboard?view=${role}`;
}

// Table sorting
function sortTable(tableId, column) {
    const table = document.getElementById(tableId);
    const rows = Array.from(table.rows);
    const isAscending = table.dataset.sortOrder !== 'asc';

    rows.sort((a, b) => {
        const aVal = a.cells[column].textContent;
        const bVal = b.cells[column].textContent;
        return isAscending ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
    });

    rows.forEach(row => table.appendChild(row));
    table.dataset.sortOrder = isAscending ? 'asc' : 'desc';
}

// Export to CSV
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    let csv = [];

    for (let row of table.rows) {
        let cols = [];
        for (let cell of row.cells) {
            cols.push(cell.textContent);
        }
        csv.push(cols.join(','));
    }

    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${filename}.csv`;
    a.click();
}

// Initialize member chart
function initMemberChart(roleData) {
    const ctx = document.getElementById('memberChart');
    if (!ctx) return;

    const chartData = [roleData.client, roleData.shareholder, roleData.cashier, roleData.td, roleData.ceo, roleData.admin];
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Client', 'Shareholder', 'Cashier', 'TD', 'CEO', 'Admin'],
            datasets: [
                {
                    type: 'line',
                    label: 'Growth Trend',
                    data: chartData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3
                },
                {
                    type: 'bar',
                    label: 'Active Members',
                    data: chartData,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(220, 38, 38, 0.8)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                        'rgb(220, 38, 38)'
                    ],
                    borderWidth: 2,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { font: { size: 8 }, boxWidth: 10 }
                },
                title: {
                    display: true,
                    text: `Total: ${roleData.total} Members | Active: ${roleData.active}`,
                    font: { size: 10, weight: 'bold' },
                    color: '#1f2937'
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: { size: 11 },
                    bodyFont: { size: 10 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { font: { size: 8 } },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    ticks: { font: { size: 8 } },
                    grid: { display: false }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initUserRole();
    initProjectSlideshow();
    initMeetingSlideshow();
    setInterval(showSlides, 3000);
    
    // Initialize chart if data is available
    const chartDataElement = document.getElementById('chartData');
    if (chartDataElement) {
        const roleData = JSON.parse(chartDataElement.textContent);
        initMemberChart(roleData);
    }
});
