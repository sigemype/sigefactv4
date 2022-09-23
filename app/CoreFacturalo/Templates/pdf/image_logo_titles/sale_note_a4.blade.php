@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $left =  ($document->series) ? $document->series : $document->prefix;
    $tittle = $left.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $payments = $document->payments;

    $configuration = \App\Models\Tenant\Configuration::first();

@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
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
                <h3>NOTA DE VENTA</h3>
                <h3 class="text-center">{{ $tittle }}</h3>
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
                        <td width="50%" style="text-align: top; vertical-align: top;">Fecha de emisión</td>
                        <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                        <td style="text-align: top; vertical-align: top;">{{$document->date_of_issue->format('Y-m-d')}}</td>
                    </tr>
                    @if ($document->total_canceled)
                        <tr>
                            <td class="align-top">Estado</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td colspan="3">CANCELADO</td>
                        </tr>
                    @else
                        <tr>
                            <td class="align-top">Estado</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td colspan="3">PENDIENTE DE PAGO</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

@if ($document->guides)
<br/>
{{--<strong>Guías:</strong>--}}
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
            <td class="text-left">
                {!!$row->item->description!!} @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

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
        <tr>
            <td colspan="7" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="7" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width border-box">
<tr>
    <td>
    <strong>PAGOS:</strong> </td></tr>
        @php
            $payment = 0;
        @endphp
        @foreach($payments as $row)
            <tr><td>- {{ $row->date_of_payment->format('d/m/Y') }} - {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment }}</td></tr>
            @php
                $payment += (float) $row->payment;
            @endphp
        @endforeach
        <tr><td><strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td>
    </tr>

</table>
@if ($document->terms_condition)
        <br>
        <table class="full-width">
            <tr>
                <td>
                    <h6 style="font-size: 12px; font-weight: bold;">Términos y condiciones del servicio</h6>
                    {!! $document->terms_condition !!}
                </td>
            </tr>
        </table>
    @endif
</body>
</html>
