@extends('layouts.shareholder')

@section('content')
@include('admin.financial.partials.transaction-show', [
    'backRoute' => route('shareholder.transactions'),
    'showEdit' => false,
])
@endsection

