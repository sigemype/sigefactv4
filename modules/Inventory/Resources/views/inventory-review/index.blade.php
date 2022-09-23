@extends('tenant.layouts.app')

@section('content')

    <inventory-review-index :type-user="{{ json_encode(auth()->user()->type) }}"></inventory-review-index>

@endsection
