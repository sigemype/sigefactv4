@extends('tenant.layouts.app')

@section('content')

    <tenant-state-account-index
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
    ></tenant-state-account-index>

@endsection
