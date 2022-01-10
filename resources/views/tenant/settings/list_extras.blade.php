@extends('tenant.layouts.app')

@section('content')
    <div class="page-header pr-0">
        <h2>
            <a href="/dashboard">
                <i class="fas fa-home"></i>
            </a>
        </h2>
        <ol class="breadcrumbs">
            <li class="active">
                <span>Dashboard</span>
            </li>
            <li>
                <span class="text-muted">Extras</span>
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-{{in_array('hotels', $vc_modules) ? 'primary' : 'dark'}}">
                    <div class="card-header-icon">
                        <i class="fas fa-hotel"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">Hoteles</h3>

                    <p class="text-center">Gestiona un edificio completo, sus pisos, habitaciones, características de
                                           cada una y sus precios.</p>
                    <span class="badge badge-{{in_array('hotels', $vc_modules) ? 'success' : 'default'}}">
                    {{in_array('hotels', $vc_modules) ? 'Activo' : 'Inactivo'}}
                </span>
                    <br>
                    @if(!in_array('hotels', $vc_modules))
                        <small class="text-muted">Debe consultar con su administrador para poder habilitarlo</small>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-{{in_array('documentary-procedure', $vc_modules) ? 'primary' : 'dark'}}">
                    <div class="card-header-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">Trámite documentario</h3>
                    <p class="text-center">Los documentos puede pasar por varias etapas y ser aprobados en cada una de
                                           ellas.</p>
                    <span class="badge badge-{{in_array('documentary-procedure', $vc_modules) ? 'success' : 'default'}}">
                    {{in_array('documentary-procedure', $vc_modules) ? 'Activo' : 'Inactivo'}}
                </span>
                    <br>
                    @if(!in_array('documentary-procedure', $vc_modules))
                        <small class="text-muted">Debe consultar con su administrador para poder habilitarlo</small>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-{{in_array('digemid', $vc_modules) ? 'primary' : 'dark'}}">
                    <div class="card-header-icon">
                        <i class="fas fa-book-medical"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">Farmacia</h3>
                    <p class="text-center">Compara tus productos con los del listado de digemid y exporta tu listado
                                           para tenerlos actualizado.</p>
                    <span class="badge badge-{{in_array('digemid', $vc_modules) ? 'success' : 'default'}}">
                    {{in_array('digemid', $vc_modules) ? 'Activo' : 'Inactivo'}}
                </span>
                    <br>
                    @if(!in_array('digemid', $vc_modules))
                        <small class="text-muted">Debe consultar con su administrador para poder habilitarlo</small>
                    @else
                        <a href="{!! \Config('extra.wiki_pharmacy') !!}" target="_blank">Wiki</a></small>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-success">
                    <div class="card-header-icon">
                        <i class="fab fa-android"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">App Móvil</h3>
                    <p class="text-center">Descarga la aplicación para tu teléfono Android y genera comprobantes como
                                           Facturas, Boletas y más.</p>
                    <span class="badge badge-success">
                    Activo
                </span>
                    <br>
                    <small class="text-muted">
                        <a href="{{$apk_url != '' ? $apk_url : '#'}}" target="_blank">Descargala aquí</a></small>
                </div>
            </section>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-secondary">
                    <div class="card-header-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">Produccion</h3>
                    <p class="text-center">Gestiona Ingreso de insumos, produccion de productos... </p>

                    <span class="badge badge-{{in_array('production_app', $vc_modules) ? 'success' : 'default'}}">
                    {{in_array('production_app', $vc_modules) ? 'Activo' : 'Inactivo'}}
                </span>
                    <br>
                    @if(!in_array('production_app', $vc_modules))
                        <small class="text-muted">Debe consultar con su administrador para poder habilitarlo</small>
                    @else
                        <a href="{!! \Config('extra.wiki_production') !!}" target="_blank">Wiki</a></small>
                        @endif
                </span>
                    <br>
                </div>
            </section>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-secondary">
                    <div class="card-header-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">Suscripciones</h3>
                    <p class="text-center">Gestiona matriculas educativas, suscripción de servicios, entre otros..</p>
                    <span class="badge badge-info">
                    Próximamente
                </span>
                    <br>
                </div>
            </section>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-secondary">
                    <div class="card-header-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">Restaurantes</h3>
                    <p class="text-center">Gestión de mesas.</p>
                    <span class="badge badge-info">
                    Próximamente
                </span>
                    <br>
                </div>
            </section>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-2">
            <section class="card mb-2">
                <header class="card-header bg-secondary">
                    <div class="card-header-icon">
                        <i class="fas fa-bus-alt"></i>
                    </div>
                </header>
                <div class="card-body text-center">
                    <h3 class="font-weight-semibold mt-3 text-center">Transporte</h3>
                    <p class="text-center">Gestión de transporte de pasajeros.</p>
                    <span class="badge badge-info">
                    Próximamente
                </span>
                    <br>
                </div>
            </section>
        </div>
    </div>
@endsection
