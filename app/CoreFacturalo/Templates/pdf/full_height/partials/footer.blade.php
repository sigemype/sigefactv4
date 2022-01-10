@php
    use App\CoreFacturalo\Helpers\Template\TemplateHelper;
    use App\Models\Tenant\Document;
	use App\Models\Tenant\SaleNote;
    if ($document != null) {
        $establishment = $document->establishment;
        $customer = $document->customer;
        $invoice = $document->invoice;
        $document_base = ($document->note) ? $document->note : null;

        //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
        $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);

        if($document_base) {

            $affected_document_number = ($document_base->affected_document) ? $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT) : $document_base->data_affected_document->series.'-'.str_pad($document_base->data_affected_document->number, 8, '0', STR_PAD_LEFT);

        } else {

            $affected_document_number = null;
        }

        $payments = $document->payments;

        // $document->load('reference_guides');

        if ($document->payments) {
            $total_payment = $document->payments->sum('payment');
            $balance = ($document->total - $total_payment) - $document->payments->sum('change');
        }


    }

    $accounts = \App\Models\Tenant\BankAccount::all();

    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');


    // Pago/Coutas detalladas
    $paymentDetailed= [];
	if(
		get_class($document) == Document::class ||
		get_class($document) == SaleNote::class
	){
        $paymentDetailed = TemplateHelper::getDetailedPayment($document);
	}
@endphp
<head>
    <link href="{{ $path_style }}" rel="stylesheet" />
</head>
<body>
@if($document != null)
    @if(!is_null($document_base))
    <table class="full-width border-box">
        <tr>
            <td class="border-box text-center">Documento Afectado:</td>
            <td class="border-box text-center">Tipo de nota:</td>
            <td class="border-box text-center">Descripción:</td>

        </tr>
        <tr>
            <td class="border-box text-center">{{ $document_base->affected_document->series }}-{{ $document_base->affected_document->number }}</td>
            <td class="border-box text-center">{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
            <td class="border-box text-center">{{ $document_base->note_description }}</td>
        </tr>
    </table>        
    @endif
    <table class="full-width border-box my-2">
        <tr>
            <td class="text-upp p-2">SON:
                @foreach(array_reverse( (array) $document->legends) as $row)
                    @if ($row->code == "1000")
                        {{ $row->value }} {{ $document->currency_type->description }}
                    @else
                        {{$row->code}}: {{ $row->value }}
                    @endif
                @endforeach
            </td>
        </tr>
    </table>
    <table class="full-width border-box">
        <tr>
            <td class="text-upp p-2">OBSERVACIONES:
                @if($document->additional_information)
                    @foreach($document->additional_information as $information)
                        @if ($information)
                            {{ $information }}
                        @endif
                    @endforeach
                @endif
            </td>
        </tr>
    </table>
    <table class="full-width mt-10 mb-10 border-bottom">
        <tr>
            <th class="border-box text-center py-1 desc">IMPORTE BRUTO</th>
            <th class="border-box text-center py-1 desc">DESCUENTOS</th>
            <th class="border-box text-center py-1 desc">TOTAL VALOR VENTA</th>
            <th class="border-box text-center py-1 desc">I.G.V. 18%</th>
            <th class="border-box text-center py-1 desc">TOTAL PRECIO VENTA</th>
            <th class="border-box text-center py-1 desc">PAGO A CUENTA</th>
            <th class="border-box text-center py-1 desc">NETO A PAGAR</th>
        </tr>
        <tr>
            <td class="border-box text-center py-1 desc">
                @if($document->total_taxed > 0)
                    {{ $document->currency_type->symbol }} {{ number_format($document->total_taxed, 2) }}
                @endif
            </td>
            <td class="border-box text-center py-1 desc">
                @if($document->total_discount > 0)
                    {{ $document->currency_type->symbol }} {{ number_format($document->total_discount, 2) }}
                @endif
            </td>
            <td class="border-box text-center py-1 desc">
                @if($document->total_taxed > 0)
                    {{ $document->currency_type->symbol }} {{ number_format($document->total_taxed, 2) }}
                @endif
            </td>
            <td class="border-box text-center py-1 desc">
                {{ $document->currency_type->symbol }} {{ number_format($document->total_igv, 2) }}
            </td>
            <td class="border-box text-center py-1 desc">
                {{ $document->currency_type->symbol }} {{ number_format($document->total, 2) }}
            </td>
            <td class="border-box text-center py-1 desc">
                @if(!empty($paymentDetailed))
                    @foreach($paymentDetailed as $detailed)
                        @foreach($detailed as $row)
                            {{--{{ isset($paymentDetailed['CUOTA'])?'Cuotas:':'Pagos:' }}--}}

                            {{ $row['description']  }} -
                            {{ $row['reference']  }}
                            {{ $row['symbol']  }}
                            {{ number_format( $row['amount'], 2) }}
                        @endforeach
                    <br>
                    @endforeach
                @endif
            </td>
            <td class="border-box text-center py-1 desc">
                {{ $document->currency_type->symbol }} {{ number_format($document->total, 2) }}
            </td>
        </tr>
    </table>
    <table class="full-width border-box my-2">
                <tr>
                    <th class="p-1 border-box">Banco</th>
                    <th class="p-1 border-box">Moneda</th>
                    <th class="p-1 border-box">N° Cuenta</th>
                    <th class="p-1 border-box">N° Cuenta Interbancaria</th>
                </tr>
                @foreach($accounts as $account)
                <tr>
                    <td class="text-center border-box">{{$account->bank->description}}</td>
                    <td class="text-center border-box">{{$account->currency_type->description}}</td>
                    <td class="text-center border-box">{{$account->number}}</td>
                    <td class="text-center border-box">
                        @if($account->cci)
                            {{$account->cci}}
                        @endif
                    </td>
                </tr>
                @endforeach
    </table>
@endif
<table class="full-width">
    <tr>
        <td class="text-center desc">Representación Impresa de {{ isset($document->document_type) ? $document->document_type->description : 'Comprobante Electrónico'  }} {{ isset($document->hash) ? 'Código Hash: '.$document->hash : '' }} <br>Para consultar el comprobante ingresar a {!! url('/buscar') !!}</td>
    </tr>
    <div class="company_logo_box" style="text-align: center; top:30%;">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."sigefact.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."sigefact.png")))}}" alt="sigefact_logo" class="" width="12%">
    </div>
</table>
</body>
