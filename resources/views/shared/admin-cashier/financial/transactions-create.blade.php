@include('shared.admin-cashier.financial.partials.transaction-form', [
    'backRoute' => route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions'),
    'formAction' => route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions.store'),
    'cancelRoute' => route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.financial.transactions'),
])