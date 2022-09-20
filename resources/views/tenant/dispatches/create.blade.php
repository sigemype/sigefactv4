@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatches-create
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        :auth-user="{{json_encode(Auth::user()->getDataOnlyAuthUser())}}"
    ></tenant-dispatches-create>
@endsection
