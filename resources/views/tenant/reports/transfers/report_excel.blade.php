<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Tranferencias</title>
    </head>
    <body>
        @if(!empty($records))
            <div class=" ">
                <table class="">
                    <thead>
                        <tr></tr>
                        <tr>
                            <th colspan="10" align="center">
                                <h3 align="center" class="title celda"><strong>Reporte de Traslados</strong></h3>
                            </th>
                        </tr>
                        <tr></tr>
                        <tr>
                            <th align="center" style="background-color:powderblue;" width="2" colspan="1" rowspan="2">#</th>
                            <th align="center" style="background-color:powderblue;" width="15" colspan="1" rowspan="2">Fecha</th>
                            <th align="center" style="background-color:powderblue;" width="80" colspan="1" rowspan="2">Descripción</th>
                            <th align="center" style="background-color:powderblue;" width="30" colspan="1" rowspan="2">Almacen de origen</th>
                            <th align="center" style="background-color:powderblue;" width="30" colspan="1" rowspan="2">Almacen de destino</th>
                            <th align="center" style="background-color:powderblue;" colspan="5">Detalle de productos</th>
                        </tr>
                        <tr>
                            <th align="center" style="background-color:cornflowerblue;">Código</th>
                            <th align="center" style="background-color:cornflowerblue;">Descripción</th>
                            <th align="center" style="background-color:cornflowerblue;">Cantidad</th>
                            <th align="center" style="background-color:cornflowerblue;">P. Compra</th>
                            <th align="center" style="background-color:cornflowerblue;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $key => $value)
                        <tr>
                            <td align="center" class="celda">{{$loop->iteration}}</td>
                            <td align="center" class="celda">{{$value->created_at}}</td>
                            <td class="celda">{{$value->description}}</td>
                            <td class="celda">{{$value->warehouse}}</td>
                            <td class="celda">{{$value->warehouse_destination}}</td>
                        </tr>
                            @foreach($value->inventory as $item)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="celda" width="10">{{$item['code']}}</td>
                                <td class="celda" width="50">{{$item['description']}}</td>
                                <td class="celda" width="8">{{$item['quantity']}}</td>
                                <td class="celda" width="10">{{number_format($item['purchase_unit_price'],2)}}</td>
                                {{-- <td class="celda" width="10">{{number_format($item['quantity'],2) * number_format($item['purchase_unit_price'],2)}}</td> --}}
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div>
                <p>No se encontraron traslados de inventario.</p>
            </div>
        @endif
    </body>
</html>
