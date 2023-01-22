@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatches-create
        :document="{{ json_encode($document) }}"
        :parent-table="{{ json_encode($parentTable) }}"
        :parent-id="{{ json_encode($parentId) }}"
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        :auth-user="{{json_encode(Auth::user()->getDataOnlyAuthUser())}}"

        @if(isset($sale_note))
            :sale_note="{{ json_encode($sale_note) }}"
        @endif

    ></tenant-dispatches-create>
@endsection
