@extends('system.layouts.app')

@section('content')

    <div class="page-header pr-0">
        <h2>
            <i class="fas fa-home"></i>
        </h2>
        <ol class="breadcrumbs">
            <li>
                <span class="text-muted">Reportes</span>
            </li>
        </ol>
    </div>

    <div class="row mt-5">
        <!-- General -->
        <div class="col-6 col-md-4 mb-4">
            <div class="card card-dashboard card-reports">
                <div class="card-body">
                    <h6 class="card-title">General</h6>
                    <ul class="card-report-links">
                        <li>
                            <a href="{{route('system.report_login_lockout.index')}}">
                                Cuentas bloquedas
                            </a>
                        </li>
                        <li>
                            <a href="{{route('system.user_not_change_password.index')}}">
                                Usuarios con contraseña desactualizada
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
