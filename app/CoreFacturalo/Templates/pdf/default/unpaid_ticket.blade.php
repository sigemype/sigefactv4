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
@if($company->logo)
<div class="text-center company_logo_box pt-5">
    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo_ticket contain">
</div>
{{--@else--}}
{{--<div class="text-center company_logo_box pt-5">--}}
    {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo_ticket contain">--}}
{{--</div>--}}
@endif
<table class="full-width">
    <tr>
        <td class="text-center"><h4>{{ $company->name }}</h4></td>
    </tr>
    <tr>
        <td class="text-center"><h5>{{ 'RUC '.$company->number }}</h5></td>
    </tr>
    <tr>
        <td class="text-center" style="text-transform: uppercase;">
            {{ ($establishment->address !== '-')? $establishment->address : '' }}
            {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
            {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
            {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
        </td>
    </tr>
    @isset($establishment->trade_address)
    <tr>
        <td class="text-center ">{{  ($establishment->trade_address !== '-')? 'D. Comercial: '.$establishment->trade_address : ''  }}</td>
    </tr>
    @endisset
    <tr>
        <td class="text-center ">{{ ($establishment->telephone !== '-')? 'Central telefónica: '.$establishment->telephone : '' }}</td>
    </tr>
    <tr>
        <td class="text-center">{{ ($establishment->email !== '-')? 'Email: '.$establishment->email : '' }}</td>
    </tr>
    @isset($establishment->web_address)
        <tr>
            <td class="text-center">{{ ($establishment->web_address !== '-')? 'Web: '.$establishment->web_address : '' }}</td>
        </tr>
    @endisset

    @isset($establishment->aditional_information)
        <tr>
            <td class="text-center pb-3">{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</td>
        </tr>
    @endisset
    <tr>
        <td class="text-center pt-3 border-top"><h4>CUENTAS POR COBRAR</h4></td>
    </tr>
    <tr>
        <td class="text-center pb-3 border-bottom"><h3>{{ $tittle_unpaid }}</h3></td>
    </tr>
</table>
<table class="full-width mt-5">
    <tr>
        <td class="align-top"><p class="desc">Cliente:</p></td>
        <td><p class="desc">{{ $document->customer->name }}</p></td>
    </tr>
    <tr >
        <td width="" class="pt-3"><p class="desc">F. Emisión:</p></td>
        @if($payments->count())

                @php
                    $payment = 0;
                @endphp
                @foreach($payments as $row)
                <td width="" class="pt-3"><p class="desc">{{ $row->date_of_payment->format('Y-m-d') }}</p></td>
                    @php
                        $payment += (float) $row->payment;
                    @endphp
                @endforeach
        @endif
    </tr>
    
    <tr>
        <td class="pt-3"><p class="desc">Documento por cobrar:</p></td>
        <td class="pt-3"><p class="desc"> {{ $tittle }}</p></td>
    </tr>
</table>


<table class="full-width w-100 mt-10 mb-10">
    <thead class="">
        <tr>
            <th class="border-top-bottom desc text-center" >CANT.</th>
            <th class="border-top-bottom desc text-center" >UNIDAD</th>
            <th class="border-top-bottom desc text-center" >DESCRIPCIÓN</th>
            <th class="border-top-bottom desc text-center" >P.UNIT</th>
            <th class="border-top-bottom desc text-center" >TOTAL</th>
        </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
    <tr>
        <td class="text-center desc-9 align-top font-bold">
            @if(((int)$row->quantity != $row->quantity))
                {{ $row->quantity }}
            @else
                {{ number_format($row->quantity, 0) }}
            @endif
        </td>
        <td class="text-center desc-9 align-top">{{ $row->item->unit_type_id }}</td>
        <td class="text-left desc-9 align-top font-bold">
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

            @if($row->item->is_set == 1)

             <br>
             @inject('itemSet', 'App\Services\ItemSetService')
             @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                 {{$item}}<br>
             @endforeach
             {{-- {{join( "-", $itemSet->getItemsSet($row->item_id) )}} --}}
            @endif

            @if($document->has_prepayment)
                <br>
                *** Pago Anticipado ***
            @endif
        </td>
        <td class="text-right desc-9 align-top">{{ number_format($row->unit_price, 2) }}</td>
        <td class="text-right desc-9 align-top font-bold">{{ number_format($row->total, 2) }}</td>
    </tr>
    <tr>
        <td colspan="5" class="border-bottom"></td>
    </tr>
    @endforeach



    @if ($document->prepayments)
        @foreach($document->prepayments as $p)
        <tr>
            <td class="text-centerdesc-9 align-top">1</td>
            <td class="text-centerdesc-9 align-top">NIU</td>
            <td class="text-leftdesc-9 align-top">
                ANTICIPO: {{($p->document_type_id == '02')? 'FACTURA':'BOLETA'}} NRO. {{$p->number}}
            </td>
            <td class="text-centerdesc-9 align-top"></td>
            <td class="text-centerdesc-9 align-top"></td>
            <td class="text-centerdesc-9 align-top"></td>
            <td class="text-rightdesc-9 align-top">-{{ number_format($p->total, 2) }}</td>
            <td class="text-rightdesc-9 align-top">0</td>
            <td class="text-rightdesc-9 align-top">-{{ number_format($p->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="border-bottom"></td>
        </tr>
        @endforeach
    @endif

        @if($document->total_exportation > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif

        @if ($document->document_type_id === '07')
            @if($document->total_taxed >= 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
            @endif
        @elseif($document->total_taxed > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif

        @if($document->total_plastic_bag_taxes > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">ICBPER: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="4" class="text-right font-bold desc">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>

        @if($document->total_isc > 0)
        <tr>
            <td colspan="4" class="text-right font-bold desc">ISC: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_isc, 2) }}</td>
        </tr>
        @endif

        @if($document->total_discount > 0 && $document->subtotal > 0)
        <tr>
            <td colspan="4" class="text-right font-bold desc">SUBTOTAL: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->subtotal, 2) }}</td>
        </tr>
        @endif

        @if($document->total_discount > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
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
                    <td colspan="4" class="text-right font-bold desc">CARGOS ({{$total_factor}}%): {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_charge, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" class="text-right font-bold desc">CARGOS: {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_charge, 2) }}</td>
                </tr>
            @endif
        @endif

        @if($document->perception)
            <tr>
                <td colspan="4" class="text-right font-bold desc"> IMPORTE TOTAL: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right font-bold desc">PERCEPCIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->perception->amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right font-bold desc">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format(($document->total + $document->perception->amount), 2) }}</td>
            </tr>
        @else
            <tr>
                <td colspan="4" class="text-right font-bold desc">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
            </tr>
        @endif

        @if(($document->retention || $document->detraction) && $document->total_pending_payment > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">M. PENDIENTE: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_pending_payment, 2) }}</td>
            </tr>
        @endif

        @if($balance < 0)

            <tr>
                <td colspan="4" class="text-right font-bold desc">VUELTO: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format(abs($balance),2, ".", "") }}</td>
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
