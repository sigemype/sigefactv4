@extends('tenant.layouts.app')

@section('content')
 
    <tenant-purchase-settlements-form :purchase_order_id="{{ json_encode($order_id) }}"></tenant-purchase-settlements-form>

@endsection