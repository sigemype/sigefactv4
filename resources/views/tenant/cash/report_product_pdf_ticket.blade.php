@php

    $establishment = $cash->user->establishment

    // $total_ = $documents->count()+100;
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
          content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>Reporte POS - {{$cash->user->name}} - {{$cash->date_opening}} {{$cash->time_opening}}</title>
    <style>
@page {

    margin: 10px;

}
       html {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-spacing: 0;
            border: 1px solid black;
        }

        .celda {
            text-align: center;
            padding: 5px;
            border: 0.1px solid black;
        }

        th {
            padding: 5px;
            text-align: center;
            border-color: #0088cc;
            border: 0.1px solid black;
        }

        .title {
            font-weight: bold;
            /*padding: 5px;*/
            font-size: 13px !important;
            text-decoration: underline;
        }

        p > strong {
            margin-left: 5px;
            font-size: 12px;
        }

        thead {
            font-weight: bold;
            background: #0088cc;
            color: white;
            text-align: center;
        }

        .td-custom {
            line-height: 0.1em;
        }

        .width-custom {
            width: 50%
        }

        .font-bold{
            font-weight: bold;
        }
        /*.full-width{
            width: 100%;
        }*/
        .desc-9{
            font-size: 9px;
        }
        .desc{
            font-size: 10px;
        }
        .text-center{
            text-align: center;
        }
        .text-left{
            text-align: left;
        }
        .text-right{
            text-align: right;
        }

table {
    border-spacing: 0;
    border-collapse: collapse;
}
table, tr, td, th {
    /*font-size: 10px !important;*/
    padding: 0px;
    margin: 0px;
}


    </style>
</head>
<body>
<div style="margin-top:-15px">
    <p align="center" class="title"><strong>Reporte Punto de Venta</strong></p>
</div>
<div>

    <table class="mb-5">
        <tr>
            <td width="" class="desc font-bold">Empresa:</td>
            <td width="" class="desc">{{$company->name}}</td>
        </tr>
        <tr>
            <td width="" class="desc font-bold">Fecha reporte:</td>
            <td width="" class="desc">{{date('Y-m-d')}}</td>
        </tr>

        <tr>
            <td width="" class="desc font-bold">Ruc:</td>
            <td width="" class="desc">{{$company->number}}</td>
        </tr>
        <tr>
            <td width="" class="desc font-bold">Establecimiento:</td>
            <td width="" class="desc">{{$establishment->address}}
                    - {{$establishment->department->description}} - {{$establishment->district->description}}</td>
        </tr>
        <tr>
            <td width="" class="desc font-bold">Vendedor:</td>
            <td width="" class="desc">{{$cash->user->name}}</td>
        </tr>
        <tr>
            <td width="" class="desc font-bold">Fecha y hora apertura:</td>
            <td width="" class="desc">{{$cash->date_opening}} {{$cash->time_opening}}</td>
        </tr>
        <tr>
            <td width="" class="desc font-bold">Estado de caja:</td>
            <td width="" class="desc">{{($cash->state) ? 'Aperturada':'Cerrada'}}</td>
        </tr>
        <tr>
            <td width="" class="desc font-bold">Fecha y hora cierre:</td>
            <td width="" class="desc">{{$cash->date_closed}} {{$cash->time_closed}}</td>
        </tr>
        <tr>
            <td width="" class="desc font-bold">Total productos vendidos</td>
            <td width="" class="desc">{{$documents->count()}}</td>
        </tr>
    </table>
<p><strong>Montos de operación: </strong></p>
</div>
@if($documents->count())
    @php
        $total = 0;
        $subTotal = 0
    @endphp
    <table class="" width="100px">
        <thead class="">
        <tr>
            <th class="border-top-bottom desc-9 text-center" width="20px" style="padding-top:4px; padding-bottom:4px; ">Cant.</th>
            <th class="border-top-bottom desc-9 text-center" width="40px" style="padding-top:4px; padding-bottom:4px; ">Producto</th>
            <th class="border-top-bottom desc-9 text-center" width="40px" style="padding-top:4px; padding-bottom:4px; ">P.Unit</th>
            <th class="border-top-bottom desc-9 text-center" width="40px" style="padding-top:4px; padding-bottom:4px; ">Total</th>
            <th class="border-top-bottom desc-9 text-center" width="40px" style="padding-top:4px; padding-bottom:4px; ">CPE</th>
        </tr>
        </thead>
        <tbody>
        @foreach($documents as $item)
            <tr>
                <td class="text-center desc-9 align-center">{{ $item['quantity'] }}</td>
                <td class="text-center desc-9 align-center">{{ $item['description'] }}</td>
                <td class="text-right desc-9">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($item['unit_price']) }}</td>
                <td class="text-right desc-9">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($item['total']) }}</td>
                <td class="text-center desc-9 align-center">{{ $item['number_full'] }}</td>
            </tr>
            @php
                $total+=$item['unit_value'];
                $subTotal+=$item['total'];
            @endphp
        @endforeach
            <tr>
                <td class="text-center desc align-center" colspan="5">-------------------------------------------------------------</td>
            </tr>
            <tr>
                <td class="text-center desc align-center font-bold" colspan="3">Totales </td>
                <td class="text-center desc align-center font-bold" colspan="2">
                    {{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($subTotal) }}
                </td>
            </tr>
            <tr>
                <td class="text-center desc align-center" colspan="5">-------------------------------------------------------------</td>
            </tr>
        </tbody>
    </table>
@else
    <div class="callout callout-info">
        <p>No se encontraron registros.</p>
    </div>
@endif

{{-- @if($documents->count())
    <div class="">
        <div class=" ">
            <table class="full-width">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>P.Unitario</th>
                    <th>Total</th>
                    <th>Comprobante</th>
                </tr>
                </thead>
                <tbody>

                @foreach($documents as $item)
                    <tr>
                        <td class="celda">{{ $loop->iteration }}</td>
                        <td class="celda">{{ $item['description'] }}</td>
                        <td class="celda">{{ $item['quantity'] }}</td>
                        <td class="celda"
                            style="text-align: right">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($item['unit_price']) }}</td>
                        <td class="celda"
                            style="text-align: right">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($item['total']) }}</td>
                        <td class="celda">{{ $item['number_full'] }}</td>
                    </tr>
                    @php
                        $total+=$item['unit_value'];
                        $subTotal+=$item['total'];
                    @endphp
                @endforeach

                <tr>
                    <td class="celda"></td>
                    <td class="celda"></td>
                    <td class="celda"></td>
                    <td class="celda"> Totales </td>
                    <td style="text-align: right">
                        {{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($subTotal) }}
                    </td>
                    <td class="celda"></td>

                </tr>
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="callout callout-info">
        <p>No se encontraron registros.</p>
    </div>
@endif --}}
</body>
</html>
