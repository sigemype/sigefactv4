@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $tittle = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $payments = $document->payments;
    $accounts = \App\Models\Tenant\BankAccount::all();

    $logo = "storage/uploads/logos/{$company->logo}";
    if($establishment->logo) {
        $logo = "{$establishment->logo}";
    }

@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>

@if($company->logo)
    <div class="text-center company_logo_box pt-5">
        <img src="data:{{mime_content_type(public_path("{$logo}"))}};base64, {{base64_encode(file_get_contents(public_path("{$logo}")))}}" alt="{{$company->name}}" class="company_logo_ticket contain">
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
    <tr>
        <td class="text-center">{{ ($establishment->email !== '-')? $establishment->email : '' }}</td>
    </tr>
    <tr>
        <td class="text-center pb-3">{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</td>
    </tr>
    <tr>
        <td class="text-center pt-3 border-top border-left border-right"><h4>NOTA DE VENTA</h4></td>
    </tr>
    <tr>
        <td class="text-center pb-3 border-bottom border-left border-right"><h3>{{ $tittle }}</h3></td>
    </tr>
</table>
<table class="full-width">
    <tr>
        <td width="" class="pt-3"><p class="desc">F. Emisión:</p></td>
        <td width="" class="pt-3"><p class="desc">{{ $document->date_of_issue->format('Y-m-d') }}</p></td>
    </tr>

    @if ($document->purchase_order)
        <tr>
            <td><p class="desc">Orden de Compra:</p></td>
            <td><p class="desc">{{ $document->purchase_order }}</p></td>
        </tr>
    @endif

    <tr>
        <td><p class="desc">Estado:</p></td>
        <td><p class="desc">{{$document->total_canceled ? 'CANCELADO' : 'PENDIENTE DE PAGO'}}</p></td>
    </tr>
    
    <tr>
        <td><p class="desc">Asesor de ventas:</p></td>
        <td><p class="desc"> @if($document->seller_id != 0){{$document->seller->name }} @else {{ $document->user->name }} @endif</p></td>
    </tr>

    @if ($document->due_date)
        <tr>
            <td width="" class="pt-3"><p class="desc">F. Vencimiento:</p></td>
            <td width="" class="pt-3"><p class="desc">{{ $document->getFormatDueDate() }}</p></td>
        </tr>
    @endif

    <tr >
        <td class="align-top mt-3 pt-3"><p class="desc">Cliente:</p></td>
        <td  class="align-top mt-3 pt-3"><p class="desc ">{{ $customer->name }}</p></td>
    </tr>
    <tr>
        <td><p class="desc">{{ $customer->identity_document_type->description }}:</p></td>
        <td><p class="desc">{{ $customer->number }}</p></td>
    </tr>
    @if ($customer->address !== '')
        <tr>
            <td class="align-top"><p class="desc">Dirección:</p></td>
            <td>
                <p class="desc">
                    {{ strtoupper($customer->address) }}
                    {{ ($customer->district_id !== '-')? ', '.strtoupper($customer->district->description) : '' }}
                    {{ ($customer->province_id !== '-')? ', '.strtoupper($customer->province->description) : '' }}
                    {{ ($customer->department_id !== '-')? '- '.strtoupper($customer->department->description) : '' }}
                </p>
            </td>
        </tr>
    @endif
    
    @if ($customer->telephone)
    <tr>
        <td><p class="desc">Celular:</p></td>
        <td><p class="desc">{{ $customer->telephone }}</p></td>
    </tr>
    @endif

    @if ($document->plate_number !== null)
    <tr>
        <td class="align-top"><p class="desc">N° Placa:</p></td>
        <td><p class="desc">{{ $document->plate_number }}</p></td>
    </tr>
    @endif
    @if ($document->observation)
        <tr>
            <td><p class="desc">Observación:</p></td>
            <td><p class="desc">{{ $document->observation }}</p></td>
        </tr>
    @endif
    @if ($document->reference_data)
        <tr>
            <td class="align-top"><p class="desc">D. Referencia:</p></td>
            <td>
                <p class="desc">
                    {{ $document->reference_data }}
                </p>
            </td>
        </tr>
    @endif

    @if ($document->isPointSystem())
        <tr>
            <td><p class="desc">P. Acumulados:</p></td>
            <td><p class="desc">{{ $document->person->accumulated_points }}</p></td>
        </tr>
        <tr>
            <td><p class="desc">Puntos por la compra:</p></td>
            <td><p class="desc">{{ $document->getPointsBySale() }}</p></td>
        </tr>
    @endif

</table>

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr>
        <th class="border-top-bottom border-left desc-9 text-left">COD.</th>
        <th class="border-top-bottom desc-9 text-center">CANT.</th>
        <th class="border-top-bottom desc-9 text-left">DESCRIPCIÓN</th>
        <th class="border-top-bottom desc-9 text-right">P.UNIT</th>
        <th class="border-top-bottom border-right desc-9 text-right pr-3">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center desc-9 align-top border-left">{{ $row->item->internal_id ?? null }}</td>

            <td class="text-center desc-9 align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-left desc-9 align-top">

                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif
                    @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif
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
                @if($row->item->is_set == 1)

                 <br>
                 @inject('itemSet', 'App\Services\ItemSetService')
                 @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                     {{$item}}<br>
                 @endforeach
                @endif
                
                @if($row->item->used_points_for_exchange ?? false)
                    <br>
                    <small>*** Canjeado por {{$row->item->used_points_for_exchange}}  puntos ***</small>
                @endif
                
            </td>
            <td class="text-right desc-9 align-top ">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right desc-9 align-top border-right pr-3">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="border-bottom border-left border-right"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
         @if($document->total_discount > 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="4" class="text-right font-bold desc">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc pr-3">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        
        @if($document->total_charge > 0 && $document->charges)
            <tr>
                <td colspan="4" class="text-right font-bold desc">CARGOS ({{$document->getTotalFactor()}}%): {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format($document->total_charge, 2) }}</td>
            </tr>
        @endif
        
        <tr>
            <td colspan="4" class="text-right font-bold desc">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc pr-3">{{ number_format($document->total, 2) }}</td>
        </tr>
        
        @php
            $change_payment = $document->getChangePayment();
        @endphp

        @if($change_payment < 0)
            <tr>
                <td colspan="4" class="text-right font-bold desc">VUELTO: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc pr-3">{{ number_format(abs($change_payment),2, ".", "") }}</td>
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
    <tr><td class="desc"><strong>PAGOS:</strong> </td></tr>
    @php
        $payment = 0;
    @endphp
    @foreach($payments as $row)
        <tr>
            @php
                $spacing = '&nbsp;&nbsp;&nbsp;';
            @endphp
            <td class="desc">
                - 
                {{ $row->date_of_payment->format('d/m/Y') }}  
                {!! $spacing !!}{{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}  
                {!! $spacing !!}{{ $row->payment_method_type->description }}
                {!! $spacing !!}{{ $row->reference ? $row->reference.' - ':'' }} 
            </td>
        </tr>
        @php
            $payment += (float) $row->payment;
        @endphp
    @endforeach
    <tr><td class="pb-10 pt-2 mt-2 desc"><strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td></tr>
</table>
@endif
@if ($document->terms_condition)
    <br>
    <table class="full-width">
        <tr>
            <td>
                <h6 style="font-size: 10px; font-weight: bold;">Términos y condiciones</h6>
                <p class="desc text-justify">
                    {!! $document->terms_condition !!}
                </p>
            </td>
        </tr>
    </table>
@endif
<br>
</body>
</html>
