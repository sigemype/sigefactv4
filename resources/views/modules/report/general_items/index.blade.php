@extends('tenant.layouts.app')

@section('content')

    <tenant-report-general-items-index
        @if(isset($typereport))
        :default-type="'{{$typereport}}'"
        @endif
        @if(isset($configuration))
        :configuration="{{$configuration}}"
        @endif
    ></tenant-report-general-items-index>


@endsection
