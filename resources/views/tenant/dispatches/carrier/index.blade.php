@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatch_carrier-index
        :type-user="{{ json_encode(auth()->user()->type) }}"
        :configuration="{{$configuration}}"
    ></tenant-dispatch_carrier-index>
@endsection
