@extends('layouts.shareholder')

@section('content')
@include('admin.financial.partials.transaction-form', [
    'backRoute' => route('shareholder.transactions'),
    'formAction' => route('shareholder.transactions.store'),
    'cancelRoute' => route('shareholder.transactions'),
])
@endsection
