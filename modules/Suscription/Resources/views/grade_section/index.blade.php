@extends('tenant.layouts.app')

@section('content')

    <div class="row">

        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Grados y secciones</span></li>
            </ol>
        </div>

        <div class="col-md-6 ui-sortable">
            <tenant-suscription-grades-index></tenant-suscription-grades-index>
        </div>
        <div class="col-md-6 ui-sortable">
            <tenant-suscription-sections-index></tenant-suscription-sections-index>
        </div>

    </div>

@endsection
