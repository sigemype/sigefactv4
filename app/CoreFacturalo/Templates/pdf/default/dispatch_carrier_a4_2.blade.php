<html>
<head>
</head>
<body>
<table class="full-width">
    <tr>
        @if($document['company_logo'])
            <td width="10%">
                <img
                    src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}"
                    alt="{{ $document['company_name'] }}" class="company_logo" style="max-width: 300px">
            </td>
        @else
            <td width="10%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
        <td width="50%" class="pl-3">
            <div class="text-left">
                <h3 class="">{{ $document['company_name'] }}</h3>
                <h4>{{ 'RUC '.$document['company_number'] }}</h4>
                <h5>{{ $document['establishment_address'] }}</h5>
                <h5>{{ $document['establishment_email'] }}</h5>
                <h5>{{ $document['establishment_telephone'] }}</h5>
            </div>
        </td>
        <td width="40%" class="border-box p-4 text-center">
            <h4 class="text-center">{{ $document->document_type->description }}</h4>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left">DESTINATARIO</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Razón Social: {{ $customer->name }}</td>
    </tr>
    <tr>
        <td>{{ $customer->identity_document_type->description }}: {{ $customer->number }}
        </td>
    </tr>
    <tr>
        @if($document->transfer_reason_type_id === '09')
            <td>Dirección: {{ $customer->address }} - {{ $customer->country->description }}
            </td>
        @else
            <td>Dirección: {{ $customer->address }}
                {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
            </td>
        @endif
    </tr>
    @if ($customer->telephone)
        <tr>
            <td>Teléfono:{{ $customer->telephone }}</td>
        </tr>
    @endif
    <tr>
        <td>Vendedor: {{ $document->user->name }}</td>
    </tr>
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left" colspan="2">ENVIO</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Fecha Emisión: {{ $document['date_of_issue'] }}</td>
    </tr>
    <tr>
        <td>Fecha Inicio de Traslado: {{ $document['date_of_shipping'] }}</td>
    </tr>
    <tr>
        <td>Peso Bruto Total({{ $document['unit_type_id'] }}): {{ $document['total_weight'] }}</td>
    </tr>
    <tr>
        <td>Punto de Partida: {{ $document['sender_address_location_id'] }}
            - {{ $document['sender_address_address'] }}</td>
    </tr>
    <tr>
        <td>Punto de Llegada: {{ $document['receiver_address_location_id'] }}
            - {{ $document['receiver_address_address'] }}</td>
    </tr>
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left">TRANSPORTE</th>
    </tr>
    </thead>
    <tbody>
    @if($document['transport_plate_number'])
        <tr>
            <td>Número de placa del vehículo: {{ $document['transport_plate_number'] }}</td>
        </tr>
    @endif
    @if($document['driver_number'])
        <tr>
            <td>Conductor: {{ $document['driver_number'] }}</td>
        </tr>
    @endif
    @if($document['driver_license'])
        <tr>
            <td>Licencia del conductor: {{ $document['driver_license'] }}</td>
        </tr>
    @endif
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead class="">
    <tr>
        <th class="border-top-bottom text-center">Item</th>
        <th class="border-top-bottom text-center">Código</th>
        <th class="border-top-bottom text-left">Descripción</th>
        <th class="border-top-bottom text-left">Modelo</th>
        <th class="border-top-bottom text-center">Unidad</th>
        <th class="border-top-bottom text-right">Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="text-center">{{ $row->item->internal_id }}</td>
            <td class="text-left">
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif

                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif
                @if($row->relation_item->is_set == 1)
                    <br>
                    @inject('itemSet', 'App\Services\ItemSetService')
                    @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                        {{$item}}<br>
                    @endforeach
                @endif

                @if($document->has_prepayment)
                    <br>
                    *** Pago Anticipado ***
                @endif
            </td>
            <td class="text-left">{{ $row->item->model ?? '' }}</td>
            <td class="text-center">{{ $row->item->unit_type_id }}</td>
            <td class="text-right">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@if($document['qr'])
    <table class="full-width">
        <tr>
            <td class="text-left">
                <img src="data:image/png;base64, {{ $document['qr'] }}" style="margin-right: -10px;"/>
            </td>
        </tr>
    </table>
@endif
</body>
</html>
