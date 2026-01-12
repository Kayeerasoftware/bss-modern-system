@extends('layouts.app')

@section('title', 'Deposit Form & Records')

@section('content')
    <div class="container mx-auto px-4 py-8 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen pt-16">
        <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg fade-in mb-8">
            <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 dark:text-white font-space">Deposit Form</h2>
            <form id="depositForm" class="space-y-6">
                @csrf
                <div>
                    <label for="memberId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Member ID</label>
                    <input type="text" id="memberId" name="member_id" placeholder="Member ID" class="form-input" required>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                    <input type="number" id="amount" name="amount" placeholder="Amount" class="form-input" required>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea id="description" name="description" placeholder="Description (e.g., monthly savings)" class="form-textarea" rows="3"></textarea>
                </div>
                <div class="text-center pt-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 text-lg">Submit Deposit</button>
                </div>
            </form>
        </div>

        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg fade-in">
            <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 dark:text-white font-space">Deposit Records</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-700 border-collapse rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="p-3 text-left">SN</th>
                            <th class="p-3 text-left">Member ID</th>
                            <th class="p-3 text-left">Amount</th>
                            <th class="p-3 text-left">Description</th>
                            <th class="p-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody id="depositsTableBody" class="divide-y divide-gray-200 dark:divide-gray-600">
                        <!-- Data will be loaded here by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const depositForm = document.getElementById('depositForm');
            const depositsTableBody = document.getElementById('depositsTableBody');

            async function fetchDeposits() {
                try {
                    const response = await fetch('/api/deposits');
                    const result = await response.json();

                    if (response.ok) {
                        depositsTableBody.innerHTML = '';
                        if (result.data && result.data.length > 0) {
                            result.data.forEach((deposit, index) => {
                                const row = `
                                    <tr>
                                        <td class="p-3 text-gray-800 dark:text-gray-100">${index + 1}</td>
                                        <td class="p-3 text-gray-800 dark:text-gray-100">${deposit.member_id}</td>
                                        <td class="p-3 text-gray-800 dark:text-gray-100">UGX ${new Intl.NumberFormat('en-UG').format(deposit.amount)}</td>
                                        <td class="p-3 text-gray-800 dark:text-gray-100">${deposit.description || 'N/A'}</td>
                                        <td class="p-3 text-gray-800 dark:text-gray-100">${new Date(deposit.created_at).toLocaleDateString()}</td>
                                    </tr>
                                `;
                                depositsTableBody.innerHTML += row;
                            });
                        } else {
                            depositsTableBody.innerHTML = '<tr><td colspan="5" class="p-3 text-center text-gray-500 dark:text-gray-400">No deposit records found.</td></tr>';
                        }
                    } else {
                        console.error('Error fetching deposits:', result.message || 'Unknown error');
                        // You might want to dispatch a global toast event here
                    }
                } catch (error) {
                    console.error('Network error fetching deposits:', error);
                    // You might want to dispatch a global toast event here
                }
            }

            depositForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch('/api/deposits', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert('Deposit submitted successfully!');
                        depositForm.reset();
                        fetchDeposits(); // Reload records after successful submission
                    } else {
                        alert('Error submitting deposit: ' + (result.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Network error submitting deposit:', error);
                    alert('Network error submitting deposit.');
                }
            });

            fetchDeposits(); // Initial load of deposit records
        });
    </script>

    <style>
        /* Custom form input styles for consistency with modern design */
        .form-input,
        .form-textarea {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
            width: 100%; /* Ensure full width */
        }
        .form-input::placeholder,
        .form-textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .form-input:focus,
        .form-textarea:focus {
            border-color: #4facfe; /* Accent gradient blue */
            box-shadow: 0 0 0 2px rgba(79, 172, 254, 0.3);
            outline: none;
            background: rgba(255, 255, 255, 0.15);
        }
    </style>
@endsection