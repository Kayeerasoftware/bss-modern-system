// Shareholder investment management
export function initShareholderInvestments() {
    loadInvestmentPortfolio();
    setupInvestmentActions();
}

function loadInvestmentPortfolio() {
    fetch('/api/shareholder/investments')
        .then(response => response.json())
        .then(data => {
            displayPortfolio(data);
        });
}

function setupInvestmentActions() {
    // Setup investment action buttons
}

function displayPortfolio(investments) {
    // Display investment portfolio
}

export function makeInvestment(projectId, amount) {
    return fetch('/api/shareholder/investments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ project_id: projectId, amount: amount })
    });
}
