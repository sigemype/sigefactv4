<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte movimientos</title>
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
            font-size: 10px;
            border: 0.1px solid black;
        }

        th {
            font-size: 10px;
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
            font-size: 13px;
        }

        thead {
            font-weight: bold;
            background: #0088cc;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
<div>
    <p align="center" class="title"><strong>Reporte movimientos</strong></p>
</div>
<div style="margin-top:20px; margin-bottom:20px;">
    <table>
        <tr>
            <td>
                <p><strong>Empresa: </strong></p>
            </td>
            <td>
                <p>{{$company->name}}</p>
            </td>
            <td>
                <p><strong>Fecha: </strong></p>
            </td>
            <td>
                <p>{{date('Y-m-d')}}</p>
            </td>
        <tr>
            <td>
                <p><strong>Ruc: </strong></p>
            </td>
            <td>
                <p>{{$company->number}}</p>
            </td>
            <td>
                <p><strong>Establecimiento: </strong></p>
            </td>
            <td>
                <p>{{ $warehouse_name }}</p>
            </td>
        </tr>
    </table>
</div>
@if(!empty($records))

    <div class="">
        <div class=" ">
            <table class="">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th class="text-left">Fecha y hora transacción</th>
                    <th class="text-left">Documento</th>
                    <th class="text-left">Almacén</th>
                    <th class="text-left">Motivo de traslado</th>
                    <th class="text-right">Entrada</th>
                    <th class="text-right">Salida</th>
                </tr>
                </thead>
                <tbody>
                @foreach($records as $key => $value)
                    <tr>
                        <td class="celda text-right">{{$loop->iteration}}</td>
                        <td class="celda text-left"> {{ $value['item_description'] }} </td>
                        <td class="celda text-left"> {{ $value['date_time'] }} </td>
                        <td class="celda text-left"> {{ $value['guide_number'] }} </td>
                        <td class="celda text-left"> {{ $value['warehouse_name'] }} </td>
                        <td class="celda text-left"> {{ $value['description'] }} </td>
                        <td class="celda text-right"> {{ $value['input'] }} </td>
                        <td class="celda text-right"> {{ $value['output'] }} </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="callout callout-info">
        <p>No se encontraron registros.</p>
    </div>
@endif
</body>
</html>
