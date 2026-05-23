@php
    return redirect()->route((request()->routeIs('cashier.*') ? 'cashier' : 'admin') . '.loans.create');
@endphp