@extends('tenant.layouts.app')

@section('content')

    <tenant-agents-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-agents-index>

@endsection