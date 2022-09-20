
@php

    $all_documents = collect($data['all_documents'])->sortBy('order_number_key');

    $income_records = $all_documents->where('type_transaction_prefix', 'income');
    
    $egress_records = $all_documents->where('type_transaction_prefix', 'egress');

@endphp


@if (count($income_records) > 0)
        
    <p align="center" class="title">Ingresos</p>
    <table>
        <thead>
        <tr>
            <th>
                #
            </th>
            <th>
                Tipo transacción
            </th>
            <th>
                Tipo documento
            </th>
            <th>
                Documento
            </th>
            <th>
                Fecha emisión
            </th>
            <th>
                Cliente/Proveedor
            </th>
            <th>
                N° Documento
            </th>
            <th>
                Moneda
            </th>
            <th>
                T.Pagado
            </th>
            <th>
                Total
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($income_records as $key => $value)
            <tr>
                <td class="celda">
                    {{ $loop->iteration }}
                </td>
                <td class="celda">
                    {{ $value['type_transaction'] }}
                </td>
                <td class="celda">
                    {{ $value['document_type_description'] }}
                </td>
                <td class="celda">
                    {{ $value['number'] }}
                </td>
                <td class="celda">
                    {{ $value['date_of_issue'] }}
                </td>
                <td class="celda">
                    {{ $value['customer_name'] }}
                </td>
                <td class="celda">
                    {{ $value['customer_number'] }}
                </td>
                <td class="celda">
                    {{ $value['currency_type_id'] }}
                </td>
                <td class="celda">
                    {{ $value['total_payments']??'0.00' }}
                </td>
                <td class="celda">
                    {{ $value['total_string'] }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endif



@if (count($egress_records) > 0)
        
    <p align="center" class="title">Egresos</p>
    <table>
        <thead>
        <tr>
            <th>
                #
            </th>
            <th>
                Tipo transacción
            </th>
            <th>
                Tipo documento
            </th>
            <th>
                Documento
            </th>
            <th>
                Fecha emisión
            </th>
            <th>
                Cliente/Proveedor
            </th>
            <th>
                N° Documento
            </th>
            <th>
                Moneda
            </th>
            <th>
                T.Pagado
            </th>
            <th>
                Total
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($egress_records as $key => $value)
            <tr>
                <td class="celda">
                    {{ $loop->iteration }}
                </td>
                <td class="celda">
                    {{ $value['type_transaction'] }}
                </td>
                <td class="celda">
                    {{ $value['document_type_description'] }}
                </td>
                <td class="celda">
                    {{ $value['number'] }}
                </td>
                <td class="celda">
                    {{ $value['date_of_issue'] }}
                </td>
                <td class="celda">
                    {{ $value['customer_name'] }}
                </td>
                <td class="celda">
                    {{ $value['customer_number'] }}
                </td>
                <td class="celda">
                    {{ $value['currency_type_id'] }}
                </td>
                <td class="celda">
                    {{ $value['total_payments']??'0.00' }}
                </td>
                <td class="celda">
                    {{ $value['total_string'] }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endif