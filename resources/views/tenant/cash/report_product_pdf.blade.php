@php

    $establishment = $cash->user->establishment

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
            padding: 5px;
            font-size: 20px !important;
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

    </style>
</head>
<body>
<div>
    <p align="center"
       class="title"><strong>Reporte Punto de Venta</strong></p>
</div>
<div style="margin-top:20px; margin-bottom:20px;">
    <table>
        <tr>
            <td class="width-custom">
                <p><strong>Empresa: </strong>{{$company->name}}</p>
            </td>
            <td class="td-custom">
                <p><strong>Fecha reporte: </strong>{{date('Y-m-d')}}</p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p><strong>Ruc: </strong>{{$company->number}}</p>
            </td>
            <td class="width-custom">
                <p><strong>Establecimiento: </strong>{{$establishment->address}}
                    - {{$establishment->department->description}} - {{$establishment->district->description}}</p>
            </td>
        </tr>

        <tr>
            <td class="td-custom">
                <p><strong>Vendedor: </strong>{{$cash->user->name}}</p>
            </td>
            <td class="td-custom">
                <p><strong>Fecha y hora apertura: </strong>{{$cash->date_opening}} {{$cash->time_opening}}</p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p><strong>Estado de caja: </strong>{{($cash->state) ? 'Aperturada':'Cerrada'}}</p>
            </td>
            @if(!$cash->state)
                <td class="td-custom">
                    <p><strong>Fecha y hora cierre: </strong>{{$cash->date_closed}} {{$cash->time_closed}}</p>
                </td>
            @endif
        </tr>
        <tr>
            <td colspan="2"
                class="td-custom">
                <p><strong>Montos de operación: </strong></p>
            </td>
        </tr>


    </table>
</div>
@if($documents->count())
    @php
        $total = 0;
        $subTotal = 0
    @endphp
    @php
        $items_id=[];
        foreach ($documents as $item) {
            $validate=in_array($item['item_id'],$items_id);
            if (!$validate) {
                $items_id[]=$item['item_id'];
            }
        }
        
        
        $allTotal=0;
        //dd($items_id);
    @endphp
    @if ($is_garage)
        @include('tenant.cash.partials.data_garage')
    @endif

    <div class="">
        <div class=" ">
            <table class="">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Sub Total</th>
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
                            style="text-align: right">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($item['unit_value']) }}</td>
                        <td class="celda"
                            style="text-align: right">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($item['sub_total']) }}</td>
                        <td class="celda">{{ $item['number_full'] }}</td>
                    </tr>
                    @php
                        $total+=$item['unit_value'];
                        $subTotal+=$item['sub_total']
                    @endphp
                @endforeach

                <tr>
                    <td class="celda"></td>
                    <td class="celda"></td>
                    <td class="celda"></td>
                    <td class="celda"> Totales </td>
                    <td class="celda" style="text-align: right">
                        {{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($subTotal) }}
                    </td>
                    <td class="celda"></td>

                </tr>
                </tbody>
            </table>
            <br>

            {{-- TOTALES --}}
            @if (!$is_garage)
                @include('tenant.cash.partials.totals_sold_items')
            @endif
            
            {{-- <table class="">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items_id as $item_id)
                    @php
                        $quantity_item=0;
                        $description='';
                        $unit_value=0;
                        $total_item=0;
                        $info='TOTAL CON IGV';
                    @endphp
                    <tr>
                    @foreach($documents as $item)
                    
                        @if ($item_id==$item['item_id'])
                            @php
                                $quantity_item+=$item['quantity'];
                                $description=$item['description'];
                                $unit_value=$item['unit_value'];
                                $total_item+=$item['total'];
                                
                            @endphp
                        @endif
                        @php
                            //dd($quantity_item);
                        @endphp
                        
                    @endforeach
                        <td class="celda">{{ $loop->iteration }}</td>
                            <td class="celda">{{ $description }}</td>
                            <td class="celda">{{ $quantity_item }}</td>
                            <td class="celda"
                                style="text-align: right">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($unit_value) }}</td>
                            <td class="celda"
                                style="text-align: right">{{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($total_item) }}</td>
                            <td >{{$info}}</td>
                        </tr>
                        @php
                            //$total+=$item['unit_value'];
                            $allTotal+=$total_item;
                        @endphp
                @endforeach
                
                <tr>
                    <td class="celda"></td>
                    <td class="celda"></td>
                    <td class="celda"></td>
                    <td class="celda"> Totales </td>
                    <td class="celda" style="text-align: right">
                        {{ App\CoreFacturalo\Helpers\Template\ReportHelper::setNumber($allTotal) }}
                    </td>
                    <td class="celda"></td>

                </tr>
                </tbody>
            </table> --}}
        </div>
    </div>
@else
    <div class="callout callout-info">
        <p>No se encontraron registros.</p>
    </div>
@endif
</body>
</html>
