// ADD THESE TO main2.js adminPanel() function

// State variables (add to init section)
fundraisingTab: 'campaigns',
contributions: [],
expenses: [],
showExpenseModal: false,
expenseForm: { fundraising_id: '', description: '', amount: '', category: 'other', expense_date: '', receipt_number: '' },
editingExpense: null,
showEditExpenseModal: false,

// Load functions (add to init())
async loadContributions() {
    try {
        const response = await fetch('/api/fundraising-contributions');
        this.contributions = await response.json();
    } catch (error) {
        console.error('Error loading contributions:', error);
        this.contributions = [];
    }
},

async loadExpenses() {
    try {
        const response = await fetch('/api/fundraising-expenses');
        this.expenses = await response.json();
    } catch (error) {
        console.error('Error loading expenses:', error);
        this.expenses = [];
    }
},

// Contribution functions
async saveContribution() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch('/api/fundraising-contributions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(this.contributionForm)
        });
        const data = await response.json();
        if (data.success) {
            await this.loadContributions();
            await this.loadFundraisings();
            this.showContributionModal = false;
            this.contributionForm = { fundraising_id: '', amount: '', contributor_name: '', contributor_email: '', contributor_phone: '', payment_method: 'cash', notes: '' };
            alert('Contribution recorded successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to record contribution'));
        }
    } catch (error) {
        console.error('Error saving contribution:', error);
        alert('Error recording contribution');
    }
},

async deleteContribution(id) {
    if (!confirm('Delete this contribution?')) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch(`/api/fundraising-contributions/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await response.json();
        if (data.success) {
            await this.loadContributions();
            await this.loadFundraisings();
            alert('Contribution deleted successfully!');
        }
    } catch (error) {
        console.error('Error deleting contribution:', error);
        alert('Error deleting contribution');
    }
},

// Expense functions
async saveExpense() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const url = this.editingExpense ? `/api/fundraising-expenses/${this.editingExpense.id}` : '/api/fundraising-expenses';
        const method = this.editingExpense ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(this.expenseForm)
        });
        const data = await response.json();
        if (data.success) {
            await this.loadExpenses();
            this.showExpenseModal = false;
            this.showEditExpenseModal = false;
            this.expenseForm = { fundraising_id: '', description: '', amount: '', category: 'other', expense_date: '', receipt_number: '' };
            this.editingExpense = null;
            alert('Expense saved successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to save expense'));
        }
    } catch (error) {
        console.error('Error saving expense:', error);
        alert('Error saving expense');
    }
},

editExpense(expense) {
    this.editingExpense = { ...expense };
    this.expenseForm = { ...expense };
    this.showEditExpenseModal = true;
},

async deleteExpense(id) {
    if (!confirm('Delete this expense?')) return;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch(`/api/fundraising-expenses/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await response.json();
        if (data.success) {
            await this.loadExpenses();
            alert('Expense deleted successfully!');
        }
    } catch (error) {
        console.error('Error deleting expense:', error);
        alert('Error deleting expense');
    }
},

// Update init() to load all data
// Add these calls to the init() function:
this.loadContributions();
this.loadExpenses();

// Also add to the setInterval refresh:
this.loadContributions();
this.loadExpenses();
