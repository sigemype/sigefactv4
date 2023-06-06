<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Guia</title>
    <style>
        html {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        th {
            padding: 5px;
            text-align: center;
            border: thin solid black;
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
<table style="border:2px solid black;">
    <tr>
        <td style="text-align: center">
            <strong>R.U.C. {{$company_number}}</strong>
        </td>
    </tr>
    <tr>
        <td style="text-align: center">
            <strong>{{$document_type_name}}
        </td>
    </tr>
    <tr>
        <td style="text-align: center">
            <strong>{{$document_number}}</strong>
        </td>
    </tr>
</table>
<table>
    <tr>
        <td>ALMACÉN:</td>
        <td>{{ $warehouse_name }}</td>
        <td>FECHA DE DOCUMENTO:</td>
        <td>{{ $document_date_of_issue }}</td>
    </tr>
    <tr>
        <td>MOTIVO:</td>
        <td>{{ $transaction_name }}</td>
        <td></td>
        <td></td>
    </tr>
</table>
<table style="border: 1px solid black;">
    <thead>
    <tr>
        <th style="text-align: center">ITEM</th>
        <th>CODIGO<br/>INTERNO</th>
        <th>DESCRIPCIÓN</th>
        <th>UNIDAD</th>
        <th style="text-align: right">CANTIDAD</th>
        <th>LOTE/SERIE</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $row)
        <tr>
            <td style="text-align: center">{{$loop->iteration}}</td>
            <td>{{$row['item_internal_id']}}</td>
            <td>{{$row['item_name']}}</td>
            <td>{{$row['unit_type_id']}}</td>
            <td style="text-align: right">{{$row['quantity']}}</td>
            <td style="text-align: right">
                @if($row['series_enabled'])
                    @foreach($row['series'] as $serie)
                        @if(!$loop->first)
                            -
                        @endif
                        {{$serie['series']}}
                    @endforeach
                @endif
                @if($row['lot_enabled'])
                    @foreach($row['lot'] as $lot)
                        @if(!$loop->first)
                            -
                        @endif
                        {{$lot['code']}}
                    @endforeach
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
