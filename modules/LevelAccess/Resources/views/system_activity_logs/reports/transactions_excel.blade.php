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
                        <th class="text-center">Fecha y hora emisión</th>
                        <th>Usuario asociado</th>
                        <th>Tipo Documento</th>
                        <th class="text-center">Documento</th>
                        <th class="text-center">Fecha y hora registro</th>
                        <th class="text-center">Fecha y hora actualización</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $value)
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td>
                            {{ $value->date_of_issue }} {{ $value->time_of_issue }}
                        </td>
                        <td>
                            {{ $value->user_name }}
                        </td>
                        <td>
                            {{ $value->document_type_description }}
                        </td>
                        <td>
                            {{ $value->number_full }}
                        </td>
                        <td>
                            {{ $value->created_at }}
                        </td>
                        <td>
                            {{ $value->updated_at }}
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
