<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
</head>
<body>
<div>
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
                        <th class="text-center">Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Transacción</th>
                        <th class="text-center">Ip</th>
                        <th class="text-center">Ubicación</th>
                        <th>Dispositivo</th>
                        <th>Tipo dispositivo</th>
                        <th>Plataforma</th>
                        <th>Navegador</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $value)
                    @php
                        $row = $value->getRowResourceAccess();
                    @endphp
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td>
                            {{ $row['date_time'] }}
                        </td>
                        <td>
                            {{ $row['user_name'] }}
                        </td>
                        <td>
                            {{ $row['system_activity_log_type_description'] }}
                        </td>
                        <td>
                            {{ $row['ip'] }}
                        </td>
                        <td>
                            {{ $row['location']['full_description'] }}
                        </td>
                        <td>
                            {{ $row['device_name'] }}
                        </td>
                        <td>
                            {{ $row['device_type_description'] }}
                        </td>
                        <td>
                            {{ $row['platform_description'] }}
                        </td>
                        <td>
                            {{ $row['browser_description'] }}
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
