@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatch_carrier-form
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        :auth-user="{{json_encode(Auth::user()->getDataOnlyAuthUser())}}"
    ></tenant-dispatch_carrier-form>
@endsection
