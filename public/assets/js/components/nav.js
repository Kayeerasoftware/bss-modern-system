// Navigation JavaScript Functions

function alignWithNavbar() {
    const navbar = document.querySelector('.topnav');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    
    if (navbar) {
        const navHeight = navbar.offsetHeight;
        
        if (sidebar) {
            sidebar.style.top = navHeight + 'px';
            sidebar.style.height = `calc(100vh - ${navHeight}px)`;
        }
        
        if (mainContent) {
            mainContent.style.paddingTop = navHeight + 'px';
        }
        
        if (toggleBtn) {
            toggleBtn.style.top = (navHeight + 4) + 'px';
        }
    }
}

function forceScrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function() {
    alignWithNavbar();
    
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', forceScrollToTop);
    });
});

window.addEventListener('resize', alignWithNavbar);
