@extends('tenant.layouts.app')

@section('content')

    <tenant-report-general-items-index
        @if(isset($typeresource))
            :typeresource="'{!! $typeresource !!}'"
            @endif
        @if(isset($typereport))
            :typeresource="'{!! $typereport !!}'"
            @endif
    ></tenant-report-general-items-index>

@endsection
