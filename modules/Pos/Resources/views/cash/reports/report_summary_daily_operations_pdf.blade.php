@php
    use App\CoreFacturalo\Helpers\Functions\GeneralPdfHelper;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte Resumen de Operaciones Diarias</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .styles-table {
            width: 100%;
            border-spacing: 0;
            border: 1px solid black;
        }
        
        .width-table {
            width: 100%;
            border-spacing: 0;
        }

        .celda {
            text-align: center;
            padding: 5px;
            border: 0.1px solid black;
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

        .celda-yellow {
            background-color:yellow;
            font-weight: bold;
        }

        .celda-orange {
            background-color: rgb(238, 181, 75);
            font-weight: bold;
        }


        .text-bold{
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div>
        <p align="center" class="title">
            <strong>Resumen de Operaciones Diarias</strong><br>
        </p>
    </div>
    <div style="margin-top:10px; margin-bottom:20px;">
        <table class="styles-table">

            <tr>
                <td class="td-custom width-custom">
                    <p>
                        <strong>Empresa:
                        </strong>{{$header_data['company_name']}}
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
                        {{$header_data['company_number']}}
                    </p>
                </td>
                <td class="width-custom">
                    <p>
                        <strong>Establecimiento:
                        </strong>
                        {{$header_data['establishment_address']}} -
                        {{$header_data['establishment_department_description']}}
                        - {{$header_data['establishment_district_description']}}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <table class="full-width width-table">
        <tr>
            <td width="48%">
                
                <table class="styles-table">
                    <thead>
                        <tr>
                            <th colspan="2">INGRESOS POR VENTAS AL CONTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="celda">TOTAL EFECTIVO</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['cash_sales_income']['total_cash']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda">TOTAL TRANSFERENCIAS</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['cash_sales_income']['total_transfer']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda text-bold">TOTAL</td>
                            <td class="celda text-bold">{{ GeneralPdfHelper::setNumberFormat($data['cash_sales_income']['total']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="4%"></td>

            <td width="48%">
                
                <table class="styles-table">
                    <thead>
                        <tr>
                            <th colspan="2">COMPRAS AL CONTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="celda">TOTAL EFECTIVO</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['purchase_cash']['total_cash']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda">TOTAL TRANSFERENCIAS</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['purchase_cash']['total_transfer']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda text-bold">TOTAL</td>
                            <td class="celda text-bold">{{ GeneralPdfHelper::setNumberFormat($data['purchase_cash']['total']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <br><br>
    
    <table class="full-width width-table">
        <tr>
            <td width="48%">
                
                <table class="styles-table">
                    <thead>
                        <tr>
                            <th colspan="2">VENTAS AL CRÉDITO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="celda">TOTAL VENTAS AL CRÉDITO</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['credit_sales']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="4%"></td>

            <td width="48%">
                
                <table class="styles-table">
                    <thead>
                        <tr>
                            <th colspan="2">COMPRAS AL CRÉDITO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="celda">TOTAL COMPRAS AL CRÉDITO</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['credit_purchases']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <br><br>
    
    <table class="full-width width-table">
        <tr>
            <td width="48%">
                
                <table class="styles-table">
                    <thead>
                        <tr>
                            <th colspan="2">AMORTIZACIÓN DE VENTAS AL CRÉDITO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="celda">TOTAL EFECTIVO</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['amortization_credit_sales']['total_cash']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda">TOTAL TRANSFERENCIAS</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['amortization_credit_sales']['total_transfer']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda text-bold">TOTAL</td>
                            <td class="celda text-bold">{{ GeneralPdfHelper::setNumberFormat($data['amortization_credit_sales']['total']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="4%"></td>

            <td width="48%">
                
                <table class="styles-table">
                    <thead>
                        <tr>
                            <th colspan="2">AMORTIZACIÓN DE COMPRAS AL CRÉDITO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="celda">TOTAL EFECTIVO</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['amortization_credit_purchases']['total_cash']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda">TOTAL TRANSFERENCIAS</td>
                            <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['amortization_credit_purchases']['total_transfer']) }}</td>
                        </tr>
                        <tr>
                            <td class="celda text-bold">TOTAL</td>
                            <td class="celda text-bold">{{ GeneralPdfHelper::setNumberFormat($data['amortization_credit_purchases']['total']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <table class="full-width width-table">
        <tr>
            <td width="48%">
                <table class="styles-table">
                    <tbody>
                        <tr>
                            <td class="celda celda-yellow">TOTAL INGRESOS AL CONTADO EN EFECTIVO</td>
                            <td class="celda celda-yellow">{{ GeneralPdfHelper::setNumberFormat($data['total_cash_sales']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="4%"></td>

            <td width="48%">
                
                <table class="styles-table">
                    <tbody>
                        <tr>
                            <td class="celda celda-orange">TOTAL COMPRAS AL CONTADO EN EFECTIVO</td>
                            <td class="celda celda-orange">{{ GeneralPdfHelper::setNumberFormat($data['total_cash_purchases']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <table class="full-width width-table">
        <tr>
            <td width="48%">
                <table class="styles-table">
                    <tbody>
                        <tr>
                            <td class="celda celda-yellow">TOTAL INGRESOS AL CONTADO POR TRANSFERENCIA</td>
                            <td class="celda celda-yellow">{{ GeneralPdfHelper::setNumberFormat($data['total_cash_sales_transfer']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="4%"></td>

            <td width="48%">
                
                <table class="styles-table">
                    <tbody>
                        <tr>
                            <td class="celda celda-orange">TOTAL COMPRAS AL CONTADO POR TRANSFERENCIA</td>
                            <td class="celda celda-orange">{{ GeneralPdfHelper::setNumberFormat($data['total_cash_purchases_transfer']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <table class="full-width styles-table">
        <thead>
            <tr>
                <th colspan="2">SALDOS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="celda">SALDO EN EFECTIVO (CAJA GENERAL)</td>
                <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['cash_balance']) }}</td>
            </tr>
            <tr>
                <td class="celda">SALDO POR TRANSFERENCIAS (BANCOS)</td>
                <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['balance_transfer']) }}</td>
            </tr>
            <tr>
                <td class="celda">SALDO TOTAL</td>
                <td class="celda">{{ GeneralPdfHelper::setNumberFormat($data['total_balance']) }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
