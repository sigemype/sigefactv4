@extends('tenant.layouts.app')

@section('content')

    <tenant-report-sale_notes-index 
            :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
            >
    </tenant-report-sale_notes-index>

@endsection