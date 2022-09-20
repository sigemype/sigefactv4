@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $accounts = \App\Models\Tenant\BankAccount::where('show_in_documents', true)->get();
    $document_base = ($document->note) ? $document->note : null;
    $payments = $document->payments;

    if($document_base) {
        $affected_document_number = ($document_base->affected_document) ? $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT) : $document_base->data_affected_document->series.'-'.str_pad($document_base->data_affected_document->number, 8, '0', STR_PAD_LEFT);

    } else {
        $affected_document_number = null;
    }

    $document->load('reference_guides');
    $total_payment = $document->payments->sum('payment');
    $balance = ($document->total - $total_payment) - $document->payments->sum('change');

    $logo = "storage/uploads/logos/{$company->logo}";
    if($establishment->logo) {
        $logo = "{$establishment->logo}";
    }

@endphp
<html>
<head></head>
<body>

<table class="full-width" style="font-family: helvetica">
    <tr>
        <td class="pt-2">
            @if($company->logo)
                <div class="text-center company_logo_box">
                    <img src="data:{{mime_content_type(public_path("{$logo}"))}};base64, {{base64_encode(file_get_contents(public_path("{$logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 60%; margin-left: 20%;">
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
        <td class="text-center pt-2 border-top"><h5>{{ $document->document_type->description }}</h5></td>
    </tr>
    <tr>
        <td class="text-center pb-2 border-bottom"><h5>{{ $document_number }}</h5></td>
    </tr>
</table>
<table class="full-width" style="font-family: helvetica">
    <tr>
        <td class="desc text-uppercase" colspan="2">
            Fecha de Emisión: {{ $document->date_of_issue->format('Y-m-d') }} <br>
            @isset($invoice->date_of_due)
                F. Vencimiento: {{ $invoice->date_of_due->format('Y-m-d') }} <br>
            @endisset
            Cliente: {{ $customer->name }} <br>
            {{ $customer->identity_document_type->description }}: {{ $customer->number }} <br>
            Dirección: {{ $customer->address }}
            {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
            {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
            {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
        </td>
    </tr>

    @if ($document->reference_data)
        <tr>
            <td class="align-top"><p class="desc-ticket text-uppercase">D. Referencia:</p></td>
            <td>
                <p class="desc-ticket text-uppercase">
                    {{ $document->reference_data }}
                </p>
            </td>
        </tr>
    @endif

    @if ($document->detraction)
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">N. Cta Detracciones:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->bank_account}}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">B/S Sujeto a detracción:</p></td>
            @inject('detractionType', 'App\Services\DetractionTypeService')
            <td><p class="desc-ticket text-uppercase">{{$document->detraction->detraction_type_id}} - {{ $detractionType->getDetractionTypeDescription($document->detraction->detraction_type_id ) }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">Método de pago:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $detractionType->getPaymentMethodTypeDescription($document->detraction->payment_method_id ) }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">Porcentaje detracción:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->percentage}}%</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">Monto detracción:</p></td>
            <td><p class="desc-ticket text-uppercase">S/ {{ $document->detraction->amount}}</p></td>
        </tr>
        @if($document->detraction->pay_constancy)
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">Constancia de pago:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->pay_constancy}}</p></td>
        </tr>
        @endif


        @if($invoice->operation_type_id == '1004')
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2">DETALLE - SERVICIOS DE TRANSPORTE DE CARGA</td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc-ticket text-uppercase">Ubigeo origen:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->origin_location_id[2] }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">Dirección origen:</td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->origin_address }}</td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc-ticket text-uppercase">Ubigeo destino:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->delivery_location_id[2] }}</p></td>
        </tr>
        <tr>

            <td  class="align-top"><p class="desc-ticket text-uppercase">Dirección destino:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->delivery_address }}</p></td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc-ticket text-uppercase">Valor referencial servicio de transporte:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->reference_value_service }}</p></td>
        </tr>
        <tr>

            <td  class="align-top"><p class="desc-ticket text-uppercase">Valor referencia carga efectiva:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->reference_value_effective_load }}</p></td>
        </tr>
        <tr>
            <td class="align-top"><p class="desc-ticket text-uppercase">Valor referencial carga útil:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->reference_value_payload }}</p></td>
        </tr>
        <tr>
            <td  class="align-top"><p class="desc-ticket text-uppercase">Detalle del viaje:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->detraction->trip_detail }}</p></td>
        </tr>
        @endif

    @endif

    @if ($document->retention)
        <br>
        <tr>
            <td colspan="2">
                <p class="desc-ticket text-uppercase"><span>Información de la retención</span></p>
            </td>
        </tr>
        <tr>
            <td><p class="desc-ticket text-uppercase">Base imponible: </p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->currency_type->symbol}} {{ $document->retention->base }} </p></td>
        </tr>
        <tr>
            <td><p class="desc-ticket text-uppercase">Porcentaje:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->retention->percentage * 100 }}%</p></td>
        </tr>
        <tr>
            <td><p class="desc-ticket text-uppercase">Monto:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->currency_type->symbol}} {{ $document->retention->amount }}</p></td>
        </tr>
    @endif

    @if ($document->purchase_order)
        <tr>
            <td><p class="desc-ticket text-uppercase">Orden de Compra:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->purchase_order }}</p></td>
        </tr>
    @endif
    @if ($document->quotation_id)
        <tr>
            <td><p class="desc-ticket text-uppercase">Cotización:</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->quotation->identifier }}</p></td>
        </tr>
    @endif
    @isset($document->quotation->delivery_date)
        <tr>
            <td><p class="desc-ticket text-uppercase">F. Entrega</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->date_of_issue->addDays($document->quotation->delivery_date)->format('d-m-Y') }}</p></td>
        </tr>
    @endisset
    @isset($document->quotation->sale_opportunity)
        <tr>
            <td><p class="desc-ticket text-uppercase">O. Venta</p></td>
            <td><p class="desc-ticket text-uppercase">{{ $document->quotation->sale_opportunity->number_full}}</p></td>
        </tr>
    @endisset
</table>

@if ($document->guides)
<table>
    @foreach($document->guides as $guide)
        <tr>
            @if(isset($guide->document_type_description))
                <td>{{ $guide->document_type_description }}</td>
            @else
                <td>{{ $guide->document_type_id }}</td>
            @endif
            <td>:</td>
            <td>{{ $guide->number }}</td>
        </tr>
    @endforeach
</table>
@endif

@if (count($document->reference_guides) > 0)
<br/>
<span>Guias de remisión</span>
<table>
    @foreach($document->reference_guides as $guide)
        <tr>
            <td>{{ $guide->series }}</td>
            <td>-</td>
            <td>{{ $guide->number }}</td>
        </tr>
    @endforeach
</table>
@endif

@if(!is_null($document_base))
<table>
    <tr>
        <td class="desc-ticket text-uppercase">Documento Afectado:</td>
        <td class="desc-ticket text-uppercase">{{ $affected_document_number }}</td>
    </tr>
    <tr>
        <td class="desc-ticket text-uppercase">Tipo de nota:</td>
        <td class="desc-ticket text-uppercase">{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
    </tr>
    <tr>
        <td class="align-top desc-ticket">Descripción:</td>
        <td class="text-left desc-ticket">{{ $document_base->note_description }}</td>
    </tr>
</table>
@endif

<table class="full-width mt-10 mb-10" style="font-family: 'Courier New', Courier, monospace">

    <tbody>
        <tr>
            <td class="border-top-bottom desc text-left"></td>
            <td class="border-top-bottom desc text-left">UdM</td>
            <td class="border-top-bottom desc text-left">DESCRIPCIÓN</td>
            <td class="border-top-bottom desc text-right" style="padding-right:5px;">P.UNIT </td>
            <td class="border-top-bottom desc text-right">TOTAL </td>
        </tr>
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
<table class="full-width" style="font-family: helvetica">
    <tr>

        @foreach(array_reverse((array) $document->legends) as $row)
            <tr>
                @if ($row->code == "1000")
                    <td class="desc-ticket text-uppercase">Son: <span class="text-uppercase">{{ $row->value }} {{ $document->currency_type->description }}</span></td>
                    @if (count((array) $document->legends)>1)
                    <tr><td class="desc-ticket"><span class="">Leyendas</span></td></tr>
                    @endif
                @else
                    <td class="desc-ticket">{{$row->code}}: {{ $row->value }}</td>
                @endif
            </tr>
        @endforeach
    </tr>
    <tr>
        <td class="desc-ticket text-uppercase">
            Vendedor:
            @if ($document->seller)
                <span>{{ $document->seller->name }}</span>
            @else
                <span>{{ $document->user->name }}</span>
            @endif
        </td>
    </tr>

    @if ($document->payment_condition_id === '01')
        @if($document->payment_method_type_id)
        <tr>
            <td class="desc-ticket">
                PAGO: {{ $document->payment_method_type->description }}
            </td>
        </tr>
        @endif
        @if($payments->count())
            <tr>
                <td class="desc-ticket">
                    PAGOS:
                </td>
            </tr>
            @foreach($payments as $row)
                <tr>
                    <td class="desc-ticket text-uppercase">&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }}
                        {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td>
                </tr>
            @endforeach
        @endif
    @else
        @php
            $paymentMethod = \App\Models\Tenant\PaymentMethodType::where('id', '09')->first();
        @endphp
        <table class="full-width" style="font-family: helvetica">
            <tr>
                <td class="desc-ticket">
                    <span>PAGOS: {{ $paymentMethod->description }}</span>
                </td>
            </tr>
                @foreach($document->fee as $key => $quote)
                    <tr>
                        <td class="desc-ticket text-uppercase">&#8226; {{ (empty($quote->getStringPaymentMethodType()) ? 'Cuota #'.( $key + 1) : $quote->getStringPaymentMethodType()) }} / Fecha: {{ $quote->date->format('d-m-Y') }} / Monto: {{ $quote->currency_type->symbol }}{{ $quote->amount }}</td>
                    </tr>
                @endforeach
            </tr>
        </table>
    @endif
    @if ($document->terms_condition)
        <tr>
            <td class="desc-ticket text-uppercase">
                <br>
                Términos y condiciones del servicio
                <br>
                {!! $document->terms_condition !!}
            </td>
        </tr>
    @endif

    <tr>
        <td class="text-center pt-2 border-top">
            <img class="qr_code" src="data:image/png;base64, {{ $document->qr }}" style="max-width: 50%"/>
        </td>
    </tr>
    <tr>
        <td class="text-center desc text-uppercase border-bottom">Código Hash: {{ $document->hash }}</td>
    </tr>


    <tr>
        <td class="desc-ticket text-uppercase">
            @foreach($document->additional_information as $information)
                @if ($information)
                    @if ($loop->first)
                        <span class=" ">Información adicional</span>
                    @endif
                    <p>{{ $information }}</p>
                @endif
            @endforeach
            <br>
            @if(in_array($document->document_type->id,['01','03']))
                @foreach($accounts as $account)
                    <br>{{$account->bank->description}}
                    <br>{{$account->currency_type->description}} N°: {{$account->number}}
                    @if($account->cci)
                        <br>CCI: {{$account->cci}}
                    @endif
                @endforeach
            @endif

        </td>
    </tr>

    <tr>
        <td class="text-center desc-9 pt-2">Para consultar el comprobante ingresar a {!! url('/buscar') !!}</td>
    </tr>
</table>

</body>
</html>
