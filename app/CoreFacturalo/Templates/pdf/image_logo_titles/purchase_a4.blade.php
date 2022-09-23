@php
    $establishment = $document->establishment;
    $supplier = $document->supplier;
    $payments = $document->payments;
    $tittle = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
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
                <h3>{{ $document->document_type->description }}</h3>
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
                            <h4>Datos del proveedor</h4>
                        </td>
                    </tr>
                    <tr>
                        <td width="15%" style="text-align: top; vertical-align: top;">
                            {{ $supplier->identity_document_type->description }}
                        </td>
                        <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                        <td>{{$supplier->number}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: top; vertical-align: top;">Cliente</td>
                        <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                        <td>{{ $supplier->name }}</td>
                    </tr>
                    @if ($supplier->address !== '')
                        <tr>
                            <td class="align-top">Dirección</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td>
                                {{ $supplier->address }}
                                {{ ($supplier->district_id !== '-')? ', '.$supplier->district->description : '' }}
                                {{ ($supplier->province_id !== '-')? ', '.$supplier->province->description : '' }}
                                {{ ($supplier->department_id !== '-')? '- '.$supplier->department->description : '' }}
                            </td>
                        </tr>
                    @endif
                    @if ($supplier->telephone)
                        <tr>
                            <td class="align-top">Teléfono:</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td>
                                {{ $supplier->telephone }}
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
                    @if($document->date_of_due)
                        <tr>
                            <td>Fecha de vencimiento:</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td>{{ $document->date_of_due->format('Y-m-d') }}</td>
                        </tr>
                    @endif
                    @if($document->purchase_order)
                        <tr>
                            <td class="align-top">O. Compra:</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td  colspan="3">{{ $document->purchase_order->number_full }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2" width="8%">CANT.</th>
        <th class="border-top-bottom text-center py-2" width="8%">UNIDAD</th>
        <th class="border-top-bottom text-left py-2">DESCRIPCIÓN</th>
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
            <td colspan="7" class="border-bottom"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
        @if($document->total_discount > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="6" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
</table>
@if($payments->count())


    <table class="full-width">
        <tr>
            <td>
                <strong>PAGOS:</strong>
            </td>
        </tr>
            @php
                $payment = 0;
            @endphp
            @foreach($payments as $row)
                <tr>
                    <td>&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td>
                </tr>
            @endforeach
        </tr>

    </table>
@endif
<table class="full-width">
    <tr>
        {{-- <td width="65%">
            @foreach($document->legends as $row)
                <p>Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
            @endforeach
            <br/>
            <strong>Información adicional</strong>
            @foreach($document->additional_information as $information)
                <p>{{ $information }}</p>
            @endforeach
        </td> --}}
    </tr>
</table>
</body>
</html>
