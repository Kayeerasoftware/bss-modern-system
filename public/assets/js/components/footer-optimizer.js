// Footer Scroll Optimizer
class FooterOptimizer {
    constructor() {
        this.init();
    }

    init() {
        // Enable smooth scrolling
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Optimize footer visibility
        this.optimizeFooter();
        
        // Add scroll performance optimization
        this.addScrollOptimization();
    }

    optimizeFooter() {
        const footer = document.querySelector('footer');
        if (!footer) return;

        // Make footer lighter with CSS
        footer.style.cssText += `
            transform: translateZ(0);
            will-change: transform;
            backface-visibility: hidden;
            perspective: 1000px;
        `;
    }

    addScrollOptimization() {
        let ticking = false;

        const optimizeScroll = () => {
            // Throttle scroll events for better performance
            if (!ticking) {
                requestAnimationFrame(() => {
                    ticking = false;
                });
                ticking = true;
            }
        };

        // Use passive listeners for better performance
        window.addEventListener('scroll', optimizeScroll, { passive: true });
        window.addEventListener('wheel', optimizeScroll, { passive: true });
        window.addEventListener('touchmove', optimizeScroll, { passive: true });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new FooterOptimizer();
});

// Add CSS optimizations
const style = document.createElement('style');
style.textContent = `
    footer {
        contain: layout style paint;
        transform: translateZ(0);
    }
    
    html {
        scroll-behavior: smooth;
    }
    
    body {
        overflow-x: hidden;
    }
`;
document.head.appendChild(style);