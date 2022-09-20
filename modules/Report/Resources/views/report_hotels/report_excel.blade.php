<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
          content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>RH</title>
    <style>
        .td-custom {
            line-height: 0.1em;
        }
        .celda {
            text-align: center;
            padding: 5px;
            border: 0.1px solid black;
        }
        .width-custom {
            width: 50%
        }
    </style>
</head>
<body>
<div>
    <h3 align="center"
        class="title"><strong>Reporte de Habitaciones de Hoteles</strong></h3>
</div>
<br>
<div style="margin-top:20px; margin-bottom:15px;">
    <table>
        <tr>
            <td class="td-custom width-custom">
                <p><b>Empresa: </b></p>
            </td>
            <td align="center">
                <p><strong>{{$company->name}}</strong></p>
            </td>
            <td>
                <p><strong>Fecha: </strong></p>
            </td>
            <td align="center">
                <p><strong>{{date('Y-m-d')}}</strong></p>
            </td>
        </tr>
        <tr>
            <td>
                <p><strong>Ruc: </strong></p>
            </td>
            <td align="center">{{$company->number}}</td>

        </tr>
        @php

        $available=0;
        $busy=0;
        $cleaning = 0;
        @endphp
        @foreach($rooms as $key => $value)
        @php
        $status=$value->status;
        if ($status === 'DISPONIBLE') {
            $available+=1;
        }else if ($status === 'OCUPADO'){
            $busy+=1;
        }
            else {
            $cleaning += 1;
        }
        @endphp
        @endforeach
        <tr>
            <td class="td-custom">
                <p><strong>Habitaciones disponibles: </strong></p>
                
            </td>
            <td align="center">{{$available}}</td>
        </tr>
        <tr>
            <td class="td-custom">
                <p><strong>Habitaciones ocupadas:</strong></p>
            </td>
            <td align="center">{{$busy}}</td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Habitaciones en limpieza: 
                    </strong>
                    
                </p>
                
            </td>
            <td align="center">{{$cleaning}}</td>
        </tr>
    </table>
</div>

{{-- @php
    dd($records);
@endphp --}}
<div class="">
    <div class=" ">
<table>
    <thead>
    <tr>
        <th align="center" colspan="4">
            <p><strong>Listado de habitaciones</strong></p>
        </th>
    </tr>
    <tr>
        <th>#</th>
        <th>Habitacion</th>
        <th>Estado</th>
        <th>Horas</th>
    </tr>
    </thead>
    <tbody>
        @php

        $available=0;
        $busy=0;
        $cleaning = 0;
        $status = 0;
        $name_room = 0;
        $hours= 0;
        $hours_total=0;
        $hours_now=0;
        $symbol = 'h';
        @endphp
    @foreach($rooms as $key => $value)
    @php
    $status=$value->status;
    if ($status === 'DISPONIBLE') {
        $available+=1;
    }else if ($status === 'OCUPADO'){
        $busy+=1;
    }
     else {
        $cleaning += 1;
    }
    $hours = $value->updated_at;
    $hours = \Carbon\Carbon::parse($hours);
    $hours_now = \Carbon\Carbon::now();
    $hours_total = $hours->diffInHours($hours_now);
    if ($hours_total === 0) {
        $hours_total = $hours->diffInMinutes($hours_now);
        $symbol = 'm';
    }
    $name_room = $value->name;
    @endphp
        
        <tr>
            <td class="celda">{{$key+1}}</td>
            <td class="celda">{{$name_room}}</td>
            <td class="celda">{{$status}}</td>
            <td class="celda">{{ $hours_total.' '.$symbol}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<br>
@if(!empty($records))
            <table class="">
                <thead>
                    <tr>
                        <th align="center" colspan="15">
                            <p><strong>Reporte General</strong></p>
                        </th>
                    </tr>
                <tr>
                    {{-- <th>#</th>
                    <th>Nombres y Apellidos</th>
                    <th> Habitación</th>
                    <th>Status de pago</th>
                    <th>Status checkout</th>
                    <th>Fecha de entrada</th>
                    <th>Hora de entrada</th>
                    <th>Fecha de salida</th>
                    <th>Hora de salida</th> --}}
                    <th># numero</th>
                    <th>Nombres y apellidos</th>
                    <th>Nacionalidad</th>
                    <th>Pais o region de residencia</th>
                    <th>Tipo de documento</th>
                    <th>Numero de documento</th>
                    <th>Fecha de ingreso</th>
                    <th>Fecha de salida</th>
                    <th>Numero de habitacion asignada</th>
                    <th>Tarifa</th>
                    <th>Arribos</th>
                    <th>Habitacion noche ocupadas</th>
                    <th>Pernoctaciones</th>
                    <th>Motivo</th>
                    <th>Categoria</th>
                </tr>
                </thead>
                <tbody>
                    @php

                    $total_days_rent=0;
                    $total_nigth_days=0;
                    $nationality = 0;
                    $country = 0;
                    @endphp
                @foreach($records as $key => $value)
                    <?php
                    /** @var \Modules\Hotel\Models\HotelRent $value */
                    /* dd($value); */
                    $days_arrival=1;
                    $customer = $value->customer;
                    $room = $value->room;
                    $rates=$value->searchRateRoom($value);
                    $rates = $rates? $rates[0]->price : 0;
                    $category = $value->room->category;
                    /* dd($category['description']); */
                    $category = $category['description'];
                    $person_details = $value->searchPersonDetails($value);
                    $person_nationality = $value->searchPersonNationality($value);
                    /* dd($person_nationality); */
                    /* $document_type = $identity_document_type[0]->description; */
                    $document_type = $person_details[0]->identity_document_type;
                    $document_type = $document_type['description'];
                    $country = $person_details[0]->country;
                    $country = $country['description'];
                    if (!is_null($person_nationality)) {
                        if (!is_null($person_nationality[0]->nationality)) {
                        $nationality = $person_nationality[0]->nationality;
                        $nationality = $nationality['description'];
                        }else{
                            $nationality = 0;
                        }
                    }
                    
                    /* dd($nationality); */
                    $date_initial = new DateTime($value->input_date);
                    $date_end = new DateTime($value ->output_date);
                    $date_rent = $date_initial->diff($date_end);
                    $days = $date_rent->format('%d');
                    $days_rent = intval($days);
                    /* dd($dat); */

                    $total_days_rent+=$days_arrival;
                    $total_nigth_days+=$days_rent;
                    ?>
                    <tr>

                        <td>{{ $key+1 }}</td>
                        <td>{{$customer->name}}</td>
                        <td>{{ $nationality }}</td>{{-- NACIONALIDAD --}}
                        <td>{{ $country }}</td>{{-- PAIS RESIDENCIA --}}
                        <td>{{ $document_type }}</td>{{-- TIPO DOCUMENTO --}}
                        <td>{{ $customer->number }}</td>{{-- # DOCUMENTO --}}
                        {{-- <td>{{$room->name ?? ''}}</td>
                        <td>{{ $value ->payment_status === "PAID" ? "Pagado" : "Debe" }}</td>
                        <td>{{$value ->status}}</td> --}}
                        <td>{{$value->input_date}}</td>
                        {{-- <td>{{$value ->input_time}}</td> --}}
                        <td>{{$value ->output_date}}</td>
                        {{-- <td>{{$value ->output_time}}</td> --}}
                        <td>{{$room->name ?? ''}}</td>{{-- # HABITACION ASIGNADA --}}
                        <td>{{ $rates }}</td>{{-- TARIFA --}}
                        <td> 1 </td>{{-- ARRIBOS --}}
                        <td>{{ $days_rent }}</td>{{-- HABITACION NOCHE OCUPADAS --}}
                        <td>{{ $days_rent }}</td>{{-- PERNOCTACIONES --}}
                        <td>{{ $value->notes }}</td>{{-- MOTIVO --}}
                        <td>{{ $category }}</td>{{-- CATEGORIA --}}

                    </tr>
                @endforeach
                    <tr>
                        <td class="celda" colspan="9"></td>
                        <td class="celda"><strong>Total</strong></td>
                        <td class="celda">{{$total_days_rent}}</td>
                        <td class="celda">{{$total_nigth_days}}</td>
                        <td class="celda">{{$total_nigth_days}}</td>
                    </tr>
                    <tr></tr>
                    
                    <tr></tr>
                </tbody>
            </table>
        </div>
    </div>
@else
    <div>
        <p>No se encontraron registros.</p>
    </div>
@endif
</body>
</html>
