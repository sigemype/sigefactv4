@extends('tenant.layouts.app')

@section('content')

    <tenant-finance-unpaid-index :type-user="{{ json_encode(auth()->user()->type) }}" :configuration="{{ $configuration }}" ></tenant-finance-unpaid-index>

@endsection
