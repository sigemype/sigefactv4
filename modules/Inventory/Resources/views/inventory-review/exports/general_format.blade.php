<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Revisión Inventario</title>
    <style>
        html {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        .title {
            font-weight: 500;
            text-align: center;
            font-size: 24px;
        }

        .label {
            width: 120px;
            font-weight: 500;
            font-family: sans-serif;
        }

        .table-records {
            margin-top: 24px;
        }

        .table-records tr th {
            font-weight: bold;
            background: #0088cc;
            color: white;
        }

        .table-records tr th,
        .table-records tr td {
            border: 1px solid #000;
            font-size: 9px;
        }
        .text-danger{
            color: red;
            background-color: red;
        }
        
    </style>
</head>
<body>
<table style="width: 100%">
    <tr>
        <td colspan="6"
            class="title"><strong>Revisión Inventario</strong></td>
    </tr>
    <tr>
        <td colspan="2"
            class="label">Empresa:
        </td>
        <td>{{$company->name}}</td>
    </tr>
    <tr>
        <td colspan="2"
            class="label">RUC:
        </td>
        <td align="left">{{$company->number}}</td>
    </tr>
    <tr>
        <td colspan="2"
            class="label">Fecha:
        </td>
        <td>{{ date('d/m/Y')}}</td>
    </tr>
</table>
<table style="width: 100%"  class="table-records">
    <thead>
        <tr>
            <th>#</th>
            <th align="left">Cód. Barras</th>
            <th align="left">Producto</th>
            <th align="center">Stock sistema</th>
            <th align="center">Stock escaneado</th>
            <th align="center">Diferencia Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $row)
            @php
            @endphp
            <tr>
                <td>{{ $loop->iteration}}</td>
                <td>{{ $row['item_barcode'] }}</td>
                <td>{{ $row['item_description'] }}</td>
                <td align="center">{{ $row['stock'] }}</td>
                <td align="center">{{ $row['input_stock'] }}</td>
                <td align="center" style="{{ ($row['difference'] < 0) ? 'color: red;': ''}}">
                    {{ $row['difference'] }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
