@include('shared.admin-cashier.financial.partials.transaction-show', [
    'backRoute' => route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions'),
    'editRoute' => route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions.edit', $transaction->id),
    'showEdit' => true,
])