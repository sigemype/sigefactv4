@php
    $establishment = $document->establishment;
    $payments = $document->payments;
    $left =  ($document->series) ? $document->series : $document->prefix;
    $tittle = $left.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $tittle_unpaid = str_pad($payments->count(), 8, '0', STR_PAD_LEFT);
    $configuration_decimal_quantity = App\CoreFacturalo\Helpers\Template\TemplateHelper::getConfigurationDecimalQuantity();
    $total_payment = $document->payments->sum('payment');
    $balance = ($document->total - $total_payment) - $document->payments->sum('change');
    //dd($tittle_unpaid);
@endphp
<html>
<head>
</head>
<body>
<table class="full-width" style="font-family: helvetica">
    <tr>
        <td class="pt-2">
            @if($company->logo)
                <div class="text-center company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" class="company_logo" style="max-width: 60%; margin-left: 20%;">
                </div>
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center text-uppercase">
            {{ $company->name }}<br>
            {{ 'RUC '.$company->number }}
        </td>
    </tr>
    <tr>
        <td class="desc text-uppercase">
             <br>
            {{ ($establishment->address !== '-')? $establishment->address : '' }}
            {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
            {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
            {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
        </td>
    </tr>
    <tr>
        <td class="desc text-uppercase">
            @isset($establishment->trade_address)
                {{  ($establishment->trade_address !== '-')? 'D. COMERCIAL: '.$establishment->trade_address : ''  }} <br>
            @endisset
                {{ ($establishment->telephone !== '-')? 'CENTRAL TELEFÓNICA: '.$establishment->telephone : '' }} <br>
                {{ ($establishment->email !== '-')? 'EMAIL: '.$establishment->email : '' }} <br>
            @isset($establishment->web_address)
                {{ ($establishment->web_address !== '-')? 'WEB: '.$establishment->web_address : '' }} <br>
            @endisset
            @isset($establishment->aditional_information)
                {{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }} <br>
            @endisset
        </td>
    </tr>
    <tr>
        <td class="text-center pt-2 border-top"><h5>CUENTAS POR COBRAR</h5></td>
    </tr>
    <tr>
        <td class="text-center pt-2 border-top"><h5>{{ $tittle_unpaid }}</h5></td>
    </tr>
</table>
<table class="full-width mt-5" style="font-family: helvetica">
    <tr>
        <td class="desc text-uppercase" colspan="2">
            Cliente: {{ $document->customer->name }} <br>
            @if($payments->count())

                    @php
                        $payment = 0;
                    @endphp
                    @foreach($payments as $row)
                    Fecha de Emisión: {{ $row->date_of_payment->format('Y-m-d') }} <br>
                        @php
                            $payment += (float) $row->payment;
                        @endphp
                    @endforeach
            @endif
        </td>
    </tr>
    <tr>
        <td class="align-top"><p class="desc-ticket text-uppercase">Documento por cobrar:</p></td>
        <td>
            <p class="desc-ticket text-uppercase">
                {{ $tittle }}
            </p>
        </td>
    </tr>
</table>


<table class="full-width mt-10 mb-10" style="font-family: 'Courier New', Courier, monospace">
    <thead class="">
        <tr>
            <td class="border-top-bottom desc text-left"></td>
            <td class="border-top-bottom desc text-left">UdM</td>
            <td class="border-top-bottom desc text-left">DESCRIPCIÓN</td>
            <td class="border-top-bottom desc text-right" style="padding-right:5px;">P.UNIT </td>
            <td class="border-top-bottom desc text-right">TOTAL </td>
        </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-left desc align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-left desc align-top">{{ $row->item->unit_type_id }}</td>
            <td class="text-left desc align-top text-uppercase">
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif

                @if($row->total_isc > 0)
                    <br/>ISC : {{ $row->total_isc }} ({{ $row->percentage_isc }}%)
                @endif

                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

                @if($row->total_plastic_bag_taxes > 0)
                    <br/>ICBPER : {{ $row->total_plastic_bag_taxes }}
                @endif

                @foreach($row->additional_information as $information)
                    @if ($information)
                        <br/>{{ $information }}
                    @endif
                @endforeach

                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/>{!! $attr->description !!} : {{ $attr->value }}
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><small>{{ $dtos->factor * 100 }}% {{$dtos->description }}</small>
                    @endforeach
                @endif

                @if($row->charges)
                    @foreach($row->charges as $charge)
                        <br/><small>{{ $document->currency_type->symbol}} {{ $charge->amount}} ({{ $charge->factor * 100 }}%) {{$charge->description }}</small>
                    @endforeach
                @endif

                @if($document->has_prepayment)
                    <br>
                    *** Pago Anticipado ***
                @endif
            </td>
            <td class="text-right desc align-top" style="padding-right:5px;">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right desc align-top">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="border-bottom"></td>
        </tr>
    @endforeach



    @if ($document->prepayments)
        @foreach($document->prepayments as $p)
        <tr>
            <td class="text-left desc align-top">1</td>
            <td class="text-left desc align-top">NIU</td>
            <td class="text-left desc align-top">
                ANTICIPO: {{($p->document_type_id == '02')? 'FACTURA':'BOLETA'}} NRO. {{$p->number}}
            </td>
            <td class="text-left desc align-top"></td>
            <td class="text-left desc align-top"></td>
            <td class="text-left desc align-top"></td>
            <td class="text-left desc align-top">-{{ number_format($p->total, 2) }}</td>
            <td class="text-left desc align-top">0</td>
            <td class="text-left desc align-top">-{{ number_format($p->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="border-bottom"></td>
        </tr>
        @endforeach
    @endif

    @if($document->total_exportation > 0)
    <tr>
        <td colspan="3" class="desc-ticket text-uppercase">OP. EXPORTACIÓN:
            {{ $document->currency_type->symbol }}</td>
        <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_exportation, 2) }}</td>
    </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">OP. GRATUITAS:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">OP. INAFECTAS:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">OP. EXONERADAS:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">OP. GRAVADAS:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
        @if($document->total_plastic_bag_taxes > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">ICBPER:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="3" class="desc-ticket text-uppercase">IGV:
                {{ $document->currency_type->symbol }}</td>
            <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_igv, 2) }}</td>
        </tr>

        @if($document->total_isc > 0)
        <tr>
            <td colspan="3" class="desc-ticket text-uppercase">ISC:
                {{ $document->currency_type->symbol }}</td>
            <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_isc, 2) }}</td>
        </tr>
        @endif

        @if($document->total_discount > 0 && $document->subtotal > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">SUBTOTAL:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->subtotal, 2) }}</td>
            </tr>
        @endif

        @if($document->total_discount > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif

        @if($document->total_charge > 0)
            @if($document->charges)
                @php
                    $total_factor = 0;
                    foreach($document->charges as $charge) {
                        $total_factor = ($total_factor + $charge->factor) * 100;
                    }
                @endphp
                <tr>
                    <td colspan="3" class="desc-ticket text-uppercase">CARGOS ({{$total_factor}}%):
                        {{ $document->currency_type->symbol }}</td>
                    <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_charge, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="3" class="desc-ticket text-uppercase">CARGOS:
                        {{ $document->currency_type->symbol }}</td>
                    <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_charge, 2) }}</td>
                </tr>
            @endif
        @endif

        <tr>
            <td colspan="3" class="desc-ticket text-uppercase">TOTAL A PAGAR:
                {{ $document->currency_type->symbol }}</td>
            <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total, 2) }}</td>
        </tr>

        @if(($document->retention || $document->detraction) && $document->total_pending_payment > 0)
            <tr>
                <td colspan="3" class="desc-ticket text-uppercase">M. PENDIENTE:
                    {{ $document->currency_type->symbol }}</td>
                <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format($document->total_pending_payment, 2) }}</td>
            </tr>
        @endif

        @if($balance < 0)
           <tr>
               <td colspan="3" class="desc-ticket text-uppercase">
                    VUELTO:
                    <span class="text-right">{{ $document->currency_type->symbol }}</span>
                </td>
               <td colspan="2" class="text-right desc-ticket text-uppercase">{{ number_format(abs($balance),2, ".", "") }}</td>
           </tr>
        @endif



    </tbody>
</table>


@if($document->payment_method_type_id && $payments->count() == 0)
    <table class="full-width">
        <tr>
            <td class="desc pt-5">
                <strong>PAGO: </strong>{{ $document->payment_method_type->description }}
            </td>
        </tr>
    </table>
@endif

@if($payments->count())

<table class="full-width">
<tr>
    <td>
    <strong>PAGOS:</strong> </td></tr>
        @php
            $payment = 0;
        @endphp
        @foreach($payments as $row)
            <tr><td>- {{ $row->date_of_payment->format('d/m/Y') }} - {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td></tr>
            @php
                $payment += (float) $row->payment;
            @endphp
        @endforeach
        <tr><td class="pb-10"><strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td>
    </tr>

</table>
@endif


</body>
</html>
