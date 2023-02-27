@extends('tenant.layouts.app')

@section('content')

    <tenant-origin_addresses-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-origin_addresses-index>

@endsection
