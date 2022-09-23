@extends('tenant.layouts.app')

@section('content')

    <tenant-users-index 
        :type-user="{{ json_encode(auth()->user()->type) }}"
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        ></tenant-users-index>

@endsection