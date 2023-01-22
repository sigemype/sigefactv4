@if ($document->transport)
    <br>
    <strong>Transporte de pasajeros</strong>
    @php
        $transport = $document->transport;
        $origin_district_id = (array)$transport->origin_district_id;
        $destinatation_district_id = (array)$transport->destinatation_district_id;
        $origin_district = Modules\Order\Services\AddressFullService::getDescription($origin_district_id[2]);
        $destinatation_district = Modules\Order\Services\AddressFullService::getDescription($destinatation_district_id[2]);
    @endphp

    <table class="full-width mt-3">
        <tr>
            <td width="120px">{{ $transport->identity_document_type->description }}</td>
            <td width="8px">:</td>
            <td>{{ $transport->number_identity_document }}</td>
            <td width="120px">NOMBRE</td>
            <td width="8px">:</td>
            <td>{{ $transport->passenger_fullname }}</td>
        </tr>
        <tr>
            <td width="120px">N° ASIENTO</td>
            <td width="8px">:</td>
            <td>{{ $transport->seat_number }}</td>
            <td width="120px">M. PASAJERO</td>
            <td width="8px">:</td>
            <td>{{ $transport->passenger_manifest }}</td>
        </tr>
        <tr>
            <td width="120px">F. INICIO</td>
            <td width="8px">:</td>
            <td>{{ $transport->start_date }}</td>
            <td width="120px">H. INICIO</td>
            <td width="8px">:</td>
            <td>{{ $transport->start_time }}</td>
        </tr>
        <tr>
            <td width="120px">U. ORIGEN</td>
            <td width="8px">:</td>
            <td>{{ $origin_district }}</td>
            <td width="120px">D. ORIGEN</td>
            <td width="8px">:</td>
            <td>{{ $transport->origin_address }}</td>
        </tr>
        <tr>
            <td width="120px">U. DESTINO</td>
            <td width="8px">:</td>
            <td>{{ $destinatation_district }}</td>
            <td width="120px">D. DESTINO</td>
            <td width="8px">:</td>
            <td>{{ $transport->destinatation_address }}</td>
        </tr>
    </table>
@endif
