@extends('tenant.layouts.app')

@section('content')

    <tenant-suscription-client-index
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
    ></tenant-suscription-client-index>

@endsection
