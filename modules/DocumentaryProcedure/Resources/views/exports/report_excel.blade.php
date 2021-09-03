<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
          content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div>
    <h3 align="center" class="title"><strong>Reporte Tramite</strong></h3>
</div>
<br>
<div style="margin-top:20px; margin-bottom:15px;">
    <table>
        <tr>
            <td>
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
            <td>
                <p><strong>Establecimiento: </strong></p>
            </td>
            <td align="center">{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</td>
        </tr>
    </table>
</div>
<br>

@if(!empty($records))
    <div class="">
        <div class=" ">
            <table class="">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Numero de expediente</th>
                    <th>Fecha/Hora registro</th>
                    <th>Remitente</th>
                    <th>Proceso</th>
                    <th>Etapa</th>
                    <th>Fecha de fin</th>
                </tr>
                </thead>
                <tbody>
                @foreach($records as $key => $value)
                    <tr>
                        <td class="text-right">{{ $key + 1 }}</td>
                        <td>{{ $value['invoice'] }}</td>
                        <td>{{ $value['date_register'] }} - {{ $value['time_register'] }}</td>
                        <td>{{ $value['sender']->name }}</td>
                        <td>
                                <span
                                >
                                     {{ $value['documentary_process']['name'] }}
                                </span>
                        </td>
                        @php($last_complete = isset($value['last_complete'])?$value['last_complete']:[])
                        <td>
                            @if(!empty($last_complete))
                                <div class="{{$last_complete['class']}}">
                                    {{ $last_complete['office_name'] }}
                                </div>
                            @endif


                        </td>
                        <td>
                            @if(!empty($last_complete))
                                <div class="{{$last_complete['class']}}">
                                    {{ $last_complete['end_date'] }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach

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
