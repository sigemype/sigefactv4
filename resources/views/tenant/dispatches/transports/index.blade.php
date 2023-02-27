@extends('tenant.layouts.app')

@section('content')

    <tenant-transports-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-transports-index>

@endsection
