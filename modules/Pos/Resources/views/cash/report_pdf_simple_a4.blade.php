<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte Simple POS - {{$data['cash_user_name']}}
        - {{$data['cash_date_opening']}} {{$data['cash_time_opening']}}
    </title>
    <style>
        body {
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

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        th {
            padding: 5px;
            text-align: center;
            border: 0.1px solid #0088cc;
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

        thead tr th {
            font-weight: bold;
            background: #0088cc;
            color: white;
            text-align: center;
        }

        .width-custom {
            width: 50%
        }
    </style>
</head>
<body>
<div>
    <p align="center" class="title">
        <strong>Reporte Simple Punto de Venta
        </strong>
    </p>
</div>
<div style="margin-top:20px; margin-bottom:20px;">
    <table>
        <tr>
            <td class="td-custom width-custom">
                <p>
                    <strong>Empresa:
                    </strong>{{$data['company_name']}}
                </p>
            </td>
            <td class="td-custom">
                <p>
                    <strong>Fecha reporte:
                    </strong>{{date('Y-m-d')}}
                </p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>Ruc:
                    </strong>
                    {{$data['company_number']}}
                </p>
            </td>
            <td class="width-custom">
                <p>
                    <strong>Establecimiento:
                    </strong>
                    {{$data['establishment_address']}} -
                    {{$data['establishment_department_description']}}
                    - {{$data['establishment_district_description']}}
                </p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>Vendedor:
                    </strong>
                    {{$data['cash_user_name']}}
                </p>
            </td>
            <td class="td-custom">
                <p>
                    <strong>Fecha y hora
                        apertura:
                    </strong>
                    {{$data['cash_date_opening']}} {{$data['cash_time_opening']}}
                </p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Estado de caja:
                    </strong>
                    {{($data['cash_state']) ? 'Aperturada':'Cerrada'}}
                </p>
            </td>
            @if(!$data['cash_state'])
                <td class="td-custom">
                    <p>
                        <strong>
                            Fecha y hora cierre:
                        </strong>
                        {{$data['cash_date_closed']}} {{$data['cash_time_closed']}}
                    </p>
                </td>
            @endif
        </tr>
        <tr>
            <td colspan="2" class="td-custom">
                <p>
                    <strong>
                        Montos de operación:
                    </strong>
                </p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Saldo inicial:
                    </strong>
                    S/. {{$data['cash_beginning_balance']}}
                </p>
            </td>
            <td class="td-custom">
                <p>
                    <strong>
                        Ingreso:
                    </strong>
                    S/. {{$data['cash_income']}}
                </p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Saldo final:
                    </strong>
                    S/. {{$data['cash_final_balance']}}
                </p>
            </td>
            <td class="td-custom">
                <p>
                    <strong>
                        Egreso:
                    </strong>
                    S/. {{ $data['cash_egress'] }}
                </p>
            </td>
        </tr>

        <tr>
            <td><hr></td>
            <td><hr></td>
        </tr>

        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Ingreso caja:
                    </strong>
                    S/. {{$data['total_cash_income_pmt_01']}}
                    {{-- total de ingresos en efectivo y destino caja --}}
                </p>
            </td>
            <td class="td-custom">
                <p>
                    <strong>
                        Egreso caja:
                    </strong>
                    S/. {{$data['total_cash_egress_pmt_01']}}
                    {{-- total de egresos (compras + gastos) en efectivo y destino caja --}}
                </p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Total caja:
                    </strong>
                    S/. {{$data['total_cash_payment_method_type_01']}}
                    {{-- (Saldo inicial + ingreso caja - egreso caja) --}}
                </p>
            </td>
            <td class="td-custom">
            </td>
        </tr>


        <tr>
            <td><hr></td>
            <td><hr></td>
        </tr>
        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Notas de Débito:
                    </strong>
                    S/. {{$data['nota_debito']}}
                </p>
            </td>
            <td class="td-custom">
                <p>
                    <strong>
                        Notas de Crédito:
                    </strong>
                    S/. {{ $data['nota_credito'] }}
                </p>
            </td>
        </tr>

        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Por cobrar:
                    </strong>
                    S/. {{ $data['credit'] }}
                </p>
            </td>

            <td class="td-custom">
                <p>
                    <strong>
                        Total propinas:
                    </strong>
                    S/. {{$data['total_tips'] ?? 0}}
                </p>
            </td>
        </tr>

        <tr>
            <td class="td-custom">
                <p>
                    <strong>
                        Total efectivo CPE:
                    </strong>
                    S/. {{$data['total_payment_cash_01_document'] ?? 0}}
                </p>
            </td>
            <td class="td-custom">
                <p>
                    <strong>
                        Total efectivo NOTA DE VENTA:
                    </strong>
                    S/. {{$data['total_payment_cash_01_sale_note'] ?? 0}}
                </p>
            </td>
        </tr>

    </table>
</div>

@if($data['cash_documents_total']>0)
    <div class="">
        <div class=" ">
            <table>
                <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th>
                        Descripcion
                    </th>
                    <th>
                        Suma
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['methods_payment'] as $item)
                    <tr>
                        <td class="celda">
                            {{ $item['iteracion'] }}
                        </td>
                        <td class="celda">
                            {{ $item['name'] }}
                        </td>
                        <td class="celda">
                            {{ $item['sum'] }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>


            @if ($data['separate_cash_transactions'])

                @include('pos::cash.partials.cash_transactions_table')

            @else

                <br>
                <table>
                    <thead>
                        <tr>
                            <th colspan="5">
                                PRODUCTOS
                            </th>
                        </tr>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                Descripción
                            </th>
                            <th>
                                Cantidad
                            </th>
                            <th>
                                Precio unitario
                            </th>
                            <th>
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    {{ $total_acum_items = 0 }}
                    @foreach($data['items_to_report']['items'] as $key => $value)
                        @php
                            $total_acum_items = $total_acum_items + $value['total'];
                        @endphp
                        <tr>
                            <td class="celda">
                                {{ $loop->iteration }}
                            </td>
                            <td class="celda text-left">
                                {{ $value['name'] }}
                            </td>
                            <td class="celda">
                                {{ $value['quantity'] }}
                            </td>
                            <td class="celda">
                                {{ $value['unit_price'] }}
                            </td>
                            <td class="celda">
                                {{ $value['total'] }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="celda" colspan="4"></td>
                        <td class="celda">
                            {{ $total_acum_items }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th colspan="4">
                                CATEGORIAS
                            </th>
                        </tr>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                Nombre
                            </th>
                            <th>
                                Cantidad
                            </th>
                            <th>
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    {{ $quantity_by_category = 0 }}
                    @foreach($data['items_to_report']['categories'] as $category)
                        <tr>
                            <td class="celda" width="10%">
                                {{ $loop->iteration }}
                            </td>
                            <td class="celda text-left">
                                {{ $category['name'] }}
                            </td>
                            <td class="celda">
                                {{ $category['quantity'] }}
                            </td>
                            <td class="celda">
                                {{ $category['total'] }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>
@else
    <div class="callout callout-info">
        <p>No se encontraron registros.
        </p>
    </div>
@endif

</body>
</html>
