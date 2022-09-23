@php
    use App\CoreFacturalo\Helpers\Template\TemplateHelper;$establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    $document_base = ($document->note) ? $document->note : null;

    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $accounts = \App\Models\Tenant\BankAccount::all();
    $configuration = \App\Models\Tenant\Configuration::first();

    if($document_base) {

        $affected_document_number = ($document_base->affected_document) ? $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT) : $document_base->data_affected_document->series.'-'.str_pad($document_base->data_affected_document->number, 8, '0', STR_PAD_LEFT);

    } else {

        $affected_document_number = null;
    }

    $payments = $document->payments;

    $document->load('reference_guides');

    $total_payment = $document->payments->sum('payment');
    $balance = ($document->total - $total_payment) - $document->payments->sum('change');

// Condicion de pago
    $condition = TemplateHelper::getDocumentPaymentCondition($document);
	// Pago/Coutas detalladas
    $paymentDetailed = TemplateHelper::getDetailedPayment($document,'d-m-Y')
@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
@if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:30%;">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
@endif

<table class="full-width" >
    <tr>
        @if($configuration->header_image)
            <td width="65%" class="pr-2">
                <div class="company_logo_box">
                    <img style="width: 90%" height="100px" src="data:{{mime_content_type(public_path("storage/uploads/header_images/{$configuration->header_image}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/header_images/{$configuration->header_image}")))}}" alt="{{$configuration->id}}" >
                </div>
            </td>
        @else
            <td width="65%"></td>
        @endif
        <td width="30%" class="border-box py-4 px-2 text-center">
            <h3>{{ 'RUC '.$company->number }}</h3>
            <h3 class="text-center">{{ $document->document_type->description }}</h3>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-3">
    <tr>
        <td width="64%" class="border-box">
            <table class="full-width">
                <tr>
                    <td colspan="3">
                        <h4>Datos del cliente</h4>
                    </td>
                </tr>
                <tr>
                    <td width="15%" style="text-align: top; vertical-align: top;">
                        {{ $customer->identity_document_type->description }}
                    </td>
                    <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                    <td>{{$customer->number}}</td>
                </tr>
                <tr>
                    <td style="text-align: top; vertical-align: top;">Cliente</td>
                    <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                    <td>{{ $customer->name }}</td>
                </tr>
                @if ($customer->address !== '')
                    <tr>
                        <td class="align-top">Dirección</td>
                        <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                        <td>
                            {{ $customer->address }}
                            {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                            {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                            {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                        </td>
                    </tr>
                @endif
            </table>
        </td>
        <td width="1%"></td>
        <td width="35%" class="border-box" style="text-align: top; vertical-align: top;">
            <table class="full-width">
                <tr>
                    <td colspan="3">
                        <h4>&nbsp;</h4>
                    </td>
                </tr>
                <tr>
                    <td width="55%" style="text-align: top; vertical-align: top;">Fecha de emisión</td>
                    <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                    <td style="text-align: top; vertical-align: top;">{{$document->date_of_issue->format('Y-m-d')}}</td>
                </tr>
                <tr>
                    @if($invoice)
                        <td width="" style="text-align: top; vertical-align: top;">Fecha de vencimiento</td>
                        <td width="" style="text-align: top; vertical-align: top;">:</td>
                        <td style="text-align: top; vertical-align: top;">{{$invoice->date_of_due->format('Y-m-d')}}</td>
                    @endif
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="full-width mt-5 border-box">
    @if ($document->detraction)
    <tr>

        <td width="120px">N. CTA DETRACCIONES</td>
        <td width="8px">:</td>
        <td>{{ $document->detraction->bank_account}}</td>


        <td width="140px">B/S SUJETO A DETRACCIÓN</td>
        <td width="8px">:</td>
        @inject('detractionType', 'App\Services\DetractionTypeService')
        <td width="220px">{{$document->detraction->detraction_type_id}} - {{ $detractionType->getDetractionTypeDescription($document->detraction->detraction_type_id ) }}</td>

    </tr>
    <tr>
        <td width="120px">MÉTODO DE PAGO</td>
        <td width="8px">:</td>
        <td width="220px">{{ $detractionType->getPaymentMethodTypeDescription($document->detraction->payment_method_id ) }}</td>

        <td width="120px">P. DETRACCIÓN</td>
        <td width="8px">:</td>
        <td>{{ $document->detraction->percentage}}%</td>
    </tr>

    <tr>
        <td width="120px">MONTO DETRACCIÓN</td>
        <td width="8px">:</td>
        <td>{{ $document->currency_type->symbol }} {{ $document->detraction->amount}}</td>

        @if($document->detraction->pay_constancy)
        <tr>
            <td colspan="3">
            </td>
            <td width="120px">CONSTANCIA DE PAGO</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->pay_constancy}}</td>
        </tr>
        @endif
    </tr>
    @endif

</table>

@if ($document->guides)
<br/>
{{--<strong>Guías:</strong>--}}
<table class="full-width mt-5 border-box">
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

@if ($document->reference_guides)
    @if (count($document->reference_guides) > 0)
        <table class="full-width mt-5 border-box">
            <tr>
                <td colspan="3">
                    <h4>Guias de remisión</h4>
                </td>
            </tr>
            @foreach($document->reference_guides as $guide)
                <tr>
                    <td>{{ $guide->series }}</td>
                    <td>-</td>
                    <td>{{ $guide->number }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@endif

<table class="full-width mt-5 border-box">
    @if ($document->prepayments)
        @foreach($document->prepayments as $p)
        <tr>
            <td width="120px">ANTICIPO</td>
            <td width="8px">:</td>
            <td>{{$p->number}}</td>
        </tr>
        @endforeach
    @endif
    @if ($document->purchase_order)
        <tr>
            <td width="120px">ORDEN DE COMPRA</td>
            <td width="8px">:</td>
            <td>{{ $document->purchase_order }}</td>
        </tr>
    @endif
    @if ($document->quotation_id)
        <tr>
            <td width="120px">COTIZACIÓN</td>
            <td width="8px">:</td>
            <td>{{ $document->quotation->identifier }}</td>

            @isset($document->quotation->delivery_date)
                    <td width="120px">F. ENTREGA</td>
                    <td width="8px">:</td>
                    <td>{{ $document->quotation->getStringDeliveryDate()}}</td>
            @endisset
        </tr>
    @endif
    @isset($document->quotation->sale_opportunity)
        <tr>
            <td width="120px">O. VENTA</td>
            <td width="8px">:</td>
            <td>{{ $document->quotation->sale_opportunity->number_full}}</td>
        </tr>
    @endisset
    @if(!is_null($document_base))
    <tr>
        <td width="120px">DOC. AFECTADO</td>
        <td width="8px">:</td>
        <td>{{ $affected_document_number }}</td>
    </tr>
    <tr>
        <td>TIPO DE NOTA</td>
        <td>:</td>
        <td>{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
    </tr>
    <tr>
        <td>DESCRIPCIÓN</td>
        <td>:</td>
        <td>{{ $document_base->note_description }}</td>
    </tr>
    @endif
</table>

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2" width="8%">CANT.</th>
        <th class="border-top-bottom text-center py-2" width="8%">UM</th>
        <th class="border-top-bottom text-center py-2" width="12%">COD</th>
        <th class="border-top-bottom text-center py-2">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-right py-2" width="12%">V/U</th>
        <th class="border-top-bottom text-right py-2" width="12%">P.UNIT</th>
        <th class="border-top-bottom text-right py-2" width="8%">DTO.</th>
        <th class="border-top-bottom text-right py-2" width="12%">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center align-top">{{ $row->item->unit_type_id }}</td>
            <td class="text-center align-top">{{ $row->item->internal_id }}</td>
            <td class="text-left align-top">
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif

                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        @if ($attr->description != "Numero de Placa")
                            <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                        @endif
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif

                @if($row->item->is_set == 1)
                 <br>
                 @inject('itemSet', 'App\Services\ItemSetService')
                    {{join( "-", $itemSet->getItemsSet($row->item_id) )}}
                @endif
            </td>
            <td class="text-right align-top">{{ number_format($row->unit_value, 2) }}</td>
            <td class="text-right align-top">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right align-top">
                @if($row->discounts)
                    @php
                        $total_discount_line = 0;
                        foreach ($row->discounts as $disto) {
                            $total_discount_line = $total_discount_line + $disto->amount;
                        }
                    @endphp
                    {{ number_format($total_discount_line, 2) }}
                @else
                0
                @endif
            </td>
            <td class="text-right align-top">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="8" class="border-bottom"></td>
        </tr>
    @endforeach



    @if ($document->prepayments)
        @foreach($document->prepayments as $p)
        <tr>
            <td class="text-center align-top">
                1
            </td>
            <td class="text-center align-top">NIU</td>
            <td class="text-left align-top">
                ANTICIPO: {{($p->document_type_id == '02')? 'FACTURA':'BOLETA'}} NRO. {{$p->number}}
            </td>
            <td class="text-right align-top">-{{ number_format($p->total, 2) }}</td>
            <td class="text-right align-top">
                0
            </td>
            <td class="text-right align-top">-{{ number_format($p->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="border-bottom"></td>
        </tr>
        @endforeach
    @endif

        @if($document->total_exportation > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
         @if($document->total_discount > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        @if($document->total_plastic_bag_taxes > 0)
            <tr>
                <td colspan="7" class="text-right font-bold">ICBPER: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="7" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>

        @if($document->perception)
            <tr>
                <td colspan="7" class="text-right font-bold"> IMPORTE TOTAL: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7" class="text-right font-bold">PERCEPCIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->perception->amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format(($document->total + $document->perception->amount), 2) }}</td>
            </tr>
        @else
            <tr>
                <td colspan="7" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
            </tr>
        @endif

        @if($balance < 0)

            <tr>
                <td colspan="7" class="text-right font-bold">VUELTO: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format(abs($balance),2, ".", "") }}</td>
            </tr>

        @endif



    </tbody>
</table>

<table class="full-width border-box mt-5">
    <tr>
        <td>
            @foreach(array_reverse( (array) $document->legends) as $row)
                @if ($row->code == "1000")
                    <p style="text-transform: uppercase;">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
                    @if (count((array) $document->legends)>1)
                        <p><span class="font-bold">Leyendas</span></p>
                    @endif
                @endif
            @endforeach
        </td>
    </tr>
</table>

<table class="full-width mt-3">
    <tr>
        <td width="74%" class="border-box" style="text-align: top; vertical-align: top;">
            <table class="full-width" >
                <tr>
                    <td>
                        <strong>CONDICIÓN DE PAGO: {{ $condition }} </strong>
                        @if($document->user)
                        <strong>| Vendedor:</strong> {{ $document->user->name }}
                        @endif
                        @if(!empty($paymentDetailed))
                            @foreach($paymentDetailed as $detailed)
                                <table class="full-width">
                                    <tr>
                                        <td>
                                            <strong>
                                                {{ isset($paymentDetailed['CUOTA'])?'Cuotas:':'Pagos:' }}
                                            </strong>
                                        </td>
                                    </tr>
                                    @foreach($detailed as $row)
                                        <tr>
                                            <td>&#8226;
                                                {{ $row['description']  }}
                                                {{ isset($paymentDetailed['CUOTA'])?' / FECHA: ':' - ' }}
                                                {{ $row['reference']  }}
                                                {{ isset($paymentDetailed['CUOTA'])?' / Monto: ':'' }}
                                                {{ $row['symbol']  }}
                                                {{ number_format( $row['amount'], 2) }}
                                            </td>
                                            @endforeach
                                        </tr>
                                </table>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="text-align: top; vertical-align: top;">
                        @if($document->payment_method_type_id)
                        <p>
                            <strong>MÉTODO DE PAGO: </strong>{{ $document->payment_method_type->description }}
                        </p>
                        @endif
                        <p>Código Hash: {{ $document->hash }}</p>
                        @foreach(array_reverse( (array) $document->legends) as $row)
                            @if ($row->code != "1000")
                                <p> {{$row->code}}: {{ $row->value }} </p>
                            @endif
                        @endforeach
                        <br/>
                        @if ($document->detraction)
                        <p>
                            <span class="font-bold">
                            Operación sujeta al Sistema de Pago de Obligaciones Tributarias
                            </span>
                        </p>
                        <br/>
                        @endif
                        @if ($customer->department_id == 16)
                            <br/><br/><br/>
                            <div>
                                <center>
                                    Representación impresa del Comprobante de Pago Electrónico.
                                    <br/>Esta puede ser consultada en:
                                    <br/><b>{!! url('/buscar') !!}</b>
                                    <br/> "Bienes transferidos en la Amazonía
                                    <br/>para ser consumidos en la misma".
                                </center>
                            </div>
                            <br/>
                        @endif
                        @foreach($document->additional_information as $information)
                            @if ($information)
                                @if ($loop->first)
                                    <strong>Información adicional</strong>
                                @endif
                                <p>{{ $information }}</p>
                            @endif
                        @endforeach
                        <br>
                        @if(in_array($document->document_type->id,['01','03']))
                            @foreach($accounts as $account)
                                <p>
                                <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                                <span class="font-bold">N°:</span> {{$account->number}}
                                @if($account->cci)
                                <span class="font-bold">CCI:</span> {{$account->cci}}
                                @endif
                                </p>
                            @endforeach
                        @endif
                        @if ($document->terms_condition)
                            <h6 style="font-size: 12px; font-weight: bold;">Términos y condiciones del servicio</h6>
                            {!! $document->terms_condition !!}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
        <td width="1%"></td>
        <td width="25%" class="border-box" style="text-align: center; vertical-align: top;">
            <img src="data:image/png;base64, {{ $document->qr }}"/>
        </td>
    </tr>
</table>

</body>
</html>
