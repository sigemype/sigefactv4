@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatches-form
        :document="{{ json_encode($document) }}"
        :document-items="{{ json_encode($items) }}"
        :type-document="{{ json_encode($type) }}"
        :dispatch="{{ json_encode($dispatch) }}"
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        :auth-user="{{json_encode(Auth::user()->getDataOnlyAuthUser())}}"

        @if(isset($sale_note))
            :sale_note="{{ json_encode($sale_note) }}"
        @endif

    ></tenant-dispatches-form>
@endsection
