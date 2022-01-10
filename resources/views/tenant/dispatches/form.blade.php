@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatches-form
        :document="{{ json_encode($document) }}"
        :document-items="{{ json_encode($document->items) }}"
        :type-document="{{ json_encode($type) }}"
        :dispatch="{{ json_encode($dispatch) }}"
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
    ></tenant-dispatches-form>
@endsection
