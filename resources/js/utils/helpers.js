// General helper functions
export function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

export function showNotification(message, type = 'success') {
    // Show toast notification
    console.log(`${type}: ${message}`);
}

export function confirmAction(message) {
    return confirm(message);
}
