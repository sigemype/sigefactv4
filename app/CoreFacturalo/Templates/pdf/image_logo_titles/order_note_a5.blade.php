@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $accounts = \App\Models\Tenant\BankAccount::all();
    $tittle = $document->prefix.'-'.str_pad($document->id, 8, '0', STR_PAD_LEFT);
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
                <h3>PEDIDO</h3>
                <h3 class="text-center">{{ $tittle }}</h3>
            </td>
        </tr>
    </table>
    <table class="mt-3">
        <tr>
            <td width="55%" class="border-box full-width">
                <table class="">
                    <tr>
                        <td colspan="3" style="font-size: initial">
                            <h4>Datos del cliente</h4>
                        </td>
                    </tr>
                    <tr style="font-size: initial">
                        <td width="" style="text-align: top; vertical-align: top;">
                            <span></span>{{ $customer->identity_document_type->description }}
                        </td>
                        <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                        <td style="">{{$customer->number}}</td>
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
                    @if ($document->account_number)
                        <tr>
                            <td class="align-top">N° Cuenta</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td colspan="3">
                                {{ $document->account_number }}
                            </td>
                        </tr>
                    @endif
                    @if ($document->shipping_address)
                        <tr>
                            <td class="align-top">Dirección de envío</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td colspan="3">
                                {{ $document->shipping_address }}
                            </td>
                        </tr>
                    @endif
                    @if ($customer->telephone)
                        <tr>
                            <td class="align-top">Teléfono</td>
                            <td width="8px" style="text-align: top; vertical-align: top;">:</td>
                            <td colspan="3">
                                {{ $customer->telephone }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
            <td width="1%"></td>
            <td width="45%" class="border-box" style="text-align: top; vertical-align: top;">
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
                    @if($document->date_of_due)
                        <tr>
                            <td>Tiempo de Validez:</td>
                            <td style="text-align: top; vertical-align: top;">:</td>
                            <td style="text-align: top; vertical-align: top;">{{ $document->date_of_due }}</td>
                        </tr>
                    @endif
                    @if($document->delivery_date)
                        <tr>
                            <td>Tiempo de Entrega:</td>
                            <td style="text-align: top; vertical-align: top;">:</td>
                            <td style="text-align: top; vertical-align: top;">{{ $document->delivery_date }}</td>
                        </tr>
                    @endif
                    @if ($document->payment_method_type)
                        <tr>
                            <td class="align-top">T. Pago:</td>
                            <td style="text-align: top; vertical-align: top;">:</td>
                            <td >
                                {{ $document->payment_method_type->description }}
                            </td>
                        </tr>
                        @if($document->sale_opportunity)
                            <tr>
                                <td colspan="3">O. Venta: {{ $document->sale_opportunity->number_full }}</td>
                            </tr>
                        @endif
                    @endif
                </table>
            </td>
        </tr>
    </table>

<table class="full-width mt-3">
    @if ($document->observation)
        <tr>
            <td width="15%" class="align-top">Observación: </td>
            <td width="85%">{{ $document->observation }}</td>
        </tr>
    @endif
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
                <td colspan="6" class="text-right font-bold">DESCUENTO TOTAL: {{ $document->currency_type->symbol }}</td>
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
<table class="full-width border-box">
    <tr>
        <td width="65%" style="text-align: top; vertical-align: top;">
            <p><strong>Vendedor:</strong> {{ $document->user->name }}</p>
            @foreach($accounts as $account)
                <p>
                <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                <span class="font-bold">N°:</span> {{$account->number}}
                @if($account->cci)
                - <span class="font-bold">CCI:</span> {{$account->cci}}
                @endif
                </p>
            @endforeach
        </td>
    </tr>
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