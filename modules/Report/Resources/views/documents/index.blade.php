@extends('tenant.layouts.app')

@section('content')

    <tenant-report-documents-index
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
    ></tenant-report-documents-index>

@endsection
