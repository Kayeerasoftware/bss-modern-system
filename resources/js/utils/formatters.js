// Data formatting utilities
export function formatCurrency(amount, currency = 'UGX') {
    return `${currency} ${Number(amount).toLocaleString()}`;
}

export function formatDate(date, format = 'short') {
    const d = new Date(date);
    if (format === 'short') {
        return d.toLocaleDateString();
    }
    return d.toLocaleString();
}

export function formatPercentage(value, decimals = 1) {
    return `${Number(value).toFixed(decimals)}%`;
}
