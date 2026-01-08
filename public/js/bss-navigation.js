class ModernNavigation {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.overlay = document.getElementById('overlay');
        this.menuToggle = document.getElementById('menuToggle');
        this.sidebarToggle = document.getElementById('sidebarToggle');
        this.closeSidebar = document.getElementById('closeSidebar');
        this.themeToggle = document.getElementById('themeToggle');
        this.profileDropdown = document.getElementById('profileDropdown');
        this.loadingOverlay = document.getElementById('loadingOverlay');
        this.sidebarSearchInput = document.getElementById('sidebarSearchInput');
        this.clearSearch = document.getElementById('clearSearch');
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.handleResize();
        this.initTheme();
        window.addEventListener('resize', () => this.handleResize());
    }
    
    bindEvents() {
        // Toggle sidebar
        this.menuToggle?.addEventListener('click', () => this.toggleSidebar());
        this.sidebarToggle?.addEventListener('click', () => this.toggleSidebarCollapse());
        this.closeSidebar?.addEventListener('click', () => this.closeSidebarMenu());
        this.overlay?.addEventListener('click', () => this.closeSidebarMenu());
        
        // Sidebar search
        this.sidebarSearchInput?.addEventListener('input', (e) => this.handleSidebarSearch(e.target.value));
        this.clearSearch?.addEventListener('click', () => this.clearSidebarSearch());
        
        // Search item click when collapsed
        const searchItem = document.querySelector('.nav-item.search-item');
        searchItem?.addEventListener('click', () => {
            if (this.sidebar.classList.contains('collapsed')) {
                this.sidebar.classList.remove('collapsed');
                setTimeout(() => this.sidebarSearchInput?.focus(), 300);
            }
        });
        
        // Theme toggle
        this.themeToggle?.addEventListener('click', () => this.toggleTheme());
        
        // Profile dropdown
        this.profileDropdown?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleProfileDropdown();
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            this.profileDropdown?.classList.remove('active');
        });
        
        // Handle navigation items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => this.handleNavClick(e));
        });
        
        // Search functionality with debounce
        const searchInput = document.querySelector('.search-box input');
        let searchTimeout;
        searchInput?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.handleSearch(e.target.value);
            }, 300);
        });
        
        // Notification buttons with animations
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleNotificationClick(e.currentTarget);
            });
        });
        
        // Page action buttons
        document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.showLoading();
                setTimeout(() => this.hideLoading(), 1500);
            });
        });
    }
    
    handleSidebarSearch(query) {
        const navItems = document.querySelectorAll('.sidebar-nav .nav-item:not(.search-item)');
        const searchQuery = query.toLowerCase().trim();
        let visibleCount = 0;
        
        // Show/hide clear button
        if (this.clearSearch) {
            this.clearSearch.style.display = query ? 'block' : 'none';
        }
        
        // Remove existing no-results message
        const existingNoResults = document.querySelector('.no-results');
        if (existingNoResults) {
            existingNoResults.remove();
        }
        
        navItems.forEach(item => {
            const text = item.querySelector('span')?.textContent.toLowerCase() || '';
            const matches = text.includes(searchQuery);
            
            if (matches || !searchQuery) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });
        
        // Show no results message
        if (visibleCount === 0 && searchQuery) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.innerHTML = '<i class="fas fa-search"></i><br>No menu items found';
            document.querySelector('.sidebar-nav').appendChild(noResults);
        }
    }
    
    clearSidebarSearch() {
        if (this.sidebarSearchInput) {
            this.sidebarSearchInput.value = '';
            this.handleSidebarSearch('');
            this.sidebarSearchInput.focus();
        }
    }
    
    initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (this.themeToggle) {
            this.themeToggle.checked = savedTheme === 'dark';
        }
        this.updateThemeIcon(savedTheme);
    }
    
    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        if (this.themeToggle) {
            this.themeToggle.checked = newTheme === 'dark';
        }
        this.updateThemeIcon(newTheme);
    }
    
    updateThemeIcon(theme) {
        const icon = this.themeToggle?.previousElementSibling;
        const label = document.getElementById('themeLabel');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        if (label) {
            label.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
        }
    }
    
    toggleProfileDropdown() {
        this.profileDropdown.classList.toggle('active');
    }
    
    showLoading() {
        this.loadingOverlay?.classList.add('active');
    }
    
    hideLoading() {
        this.loadingOverlay?.classList.remove('active');
    }
    
    handleNotificationClick(button) {
        // Add click animation
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
        
        // Remove badge if present
        const badge = button.querySelector('.badge');
        if (badge) {
            badge.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => badge.remove(), 300);
        }
        
        console.log('Notification clicked');
    }
    
    toggleSidebar() {
        const isActive = this.sidebar.classList.contains('active');
        
        if (isActive) {
            this.closeSidebarMenu();
        } else {
            this.openSidebarMenu();
        }
    }
    
    toggleSidebarCollapse() {
        this.sidebar.classList.toggle('collapsed');
        
        // Ensure sidebar is active when toggling collapse
        if (!this.sidebar.classList.contains('active')) {
            this.sidebar.classList.add('active');
        }
    }
    
    openSidebarMenu() {
        this.sidebar.classList.add('active');
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    closeSidebarMenu() {
        this.sidebar.classList.remove('active');
        this.overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    handleNavClick(e) {
        e.preventDefault();
        
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to clicked item
        e.currentTarget.classList.add('active');
        
        // Check if Settings was clicked
        if (e.currentTarget.id === 'settingsNav') {
            document.querySelector('.settings-panel').style.display = 'block';
            document.querySelector('.page-header').parentElement.querySelectorAll(':scope > :not(.settings-panel)').forEach(el => {
                if (!el.classList.contains('settings-panel')) el.style.display = 'none';
            });
        } else {
            document.querySelector('.settings-panel').style.display = 'none';
            document.querySelector('.page-header').parentElement.querySelectorAll(':scope > :not(.settings-panel)').forEach(el => {
                if (!el.classList.contains('settings-panel')) el.style.display = '';
            });
        }
        
        // Close sidebar on mobile after navigation
        if (window.innerWidth < 1024) {
            this.closeSidebarMenu();
        }
        
        // Get the navigation text
        const navText = e.currentTarget.querySelector('span')?.textContent || '';
        console.log(`Navigating to: ${navText}`);
        
        // Update main content if not settings
        if (e.currentTarget.id !== 'settingsNav') {
            this.updateMainContent(navText);
        }
    }
    
    updateMainContent(section) {
        const contentTitle = document.querySelector('.header-content h1');
        const contentDescription = document.querySelector('.header-content p');
        const breadcrumbSpan = document.querySelector('.breadcrumb span');
        
        if (contentTitle && contentDescription) {
            contentTitle.innerHTML = `${section} Dashboard 📈`;
            contentDescription.textContent = `Manage your ${section.toLowerCase()} with advanced tools and insights.`;
        }
        
        if (breadcrumbSpan) {
            breadcrumbSpan.textContent = section;
        }
        
        // Animate content change
        const content = document.querySelector('.content');
        content.style.opacity = '0.7';
        content.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            content.style.opacity = '1';
            content.style.transform = 'translateY(0)';
        }, 200);
    }
    
    handleSearch(query) {
        console.log(`Searching for: ${query}`);
        
        if (query.length > 2) {
            this.showLoading();
            
            // Simulate API call
            setTimeout(() => {
                this.hideLoading();
                console.log('Search results loaded');
                
                // Show search suggestions (placeholder)
                this.showSearchSuggestions(query);
            }, 800);
        }
    }
    
    showSearchSuggestions(query) {
        // This would typically show a dropdown with search results
        console.log(`Showing suggestions for: ${query}`);
    }
    
    handleResize() {
        const isDesktop = window.innerWidth >= 1024;
        
        if (isDesktop) {
            // Desktop behavior
            this.sidebar.classList.remove('active');
            this.overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
}

// Enhanced animations and interactions
document.addEventListener('DOMContentLoaded', () => {
    // Initialize navigation
    new ModernNavigation();
    
    // Smooth page load animation
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);
    
    // Staggered card animations
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
    // Progress bar animation
    setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    }, 1000);
    
    // Tooltip system
    initTooltips();
    
    // Parallax effect for header
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const header = document.querySelector('.page-header');
        if (header) {
            header.style.transform = `translateY(${scrolled * 0.1}px)`;
        }
    });
});

// Tooltip system
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', (e) => {
            showTooltip(e.target, e.target.getAttribute('data-tooltip'));
        });
        
        element.addEventListener('mouseleave', () => {
            hideTooltip();
        });
    });
}

function showTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.bottom + 10 + 'px';
    
    setTimeout(() => tooltip.classList.add('show'), 10);
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.classList.remove('show');
        setTimeout(() => tooltip.remove(), 200);
    }
}

// Enhanced keyboard navigation
document.addEventListener('keydown', (e) => {
    // ESC key closes sidebar and dropdowns
    if (e.key === 'Escape') {
        const sidebar = document.getElementById('sidebar');
        const profileDropdown = document.getElementById('profileDropdown');
        
        if (sidebar?.classList.contains('active')) {
            sidebar.classList.remove('active');
            document.getElementById('overlay')?.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        if (profileDropdown?.classList.contains('active')) {
            profileDropdown.classList.remove('active');
        }
    }
    
    // Ctrl/Cmd + K for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('.search-box input');
        searchInput?.focus();
        searchInput?.select();
    }
    
    // Ctrl/Cmd + D for dark mode toggle
    if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
        e.preventDefault();
        document.getElementById('themeToggle')?.click();
    }
});

// Enhanced ripple effect
document.querySelectorAll('.nav-btn, .menu-toggle, .close-btn, .btn-primary, .btn-secondary').forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    });
});

// Add enhanced CSS animations
const enhancedCSS = `
.tooltip {
    position: absolute;
    background: var(--text-primary);
    color: var(--bg-primary);
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    z-index: 10000;
    opacity: 0;
    transform: translateY(-5px);
    transition: all 0.2s ease;
    pointer-events: none;
    white-space: nowrap;
}

.tooltip.show {
    opacity: 1;
    transform: translateY(0);
}

.tooltip::before {
    content: '';
    position: absolute;
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-bottom: 4px solid var(--text-primary);
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(99, 102, 241, 0.3);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

@keyframes fadeOut {
    to {
        opacity: 0;
        transform: scale(0.8);
    }
}

.content {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Smooth hover effects */
.nav-item, .card, .btn-primary, .btn-secondary {
    will-change: transform;
}

/* Enhanced focus styles */
*:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}
`;

const style = document.createElement('style');
style.textContent = enhancedCSS;
document.head.appendChild(style);