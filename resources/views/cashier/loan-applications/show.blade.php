@extends(request()->routeIs('cashier.*') ? 'layouts.cashier' : 'layouts.admin')

@section('content')
@include('shared.admin-cashier.loan-applications.show')
@endsection
