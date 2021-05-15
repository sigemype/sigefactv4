@php
    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $accounts = \App\Models\Tenant\BankAccount::where('show_in_documents', true)->get();
@endphp
<head>
    <link href="{{ $path_style }}" rel="stylesheet" />
</head>
<body>

    

<table class="full-width">
    <tr>
        <td class="text-center desc font-bold">
            Para consultar el comprobante ingresar a {!! url('/buscar') !!}
            @if(in_array($document->document_type->id,['01','03']))
                @if ($accounts != "[]")
                    <table class="full-width desc">
                        <thead>
                            <tr>
                                <th colspan="4" class="desc">
                                    <strong>Cuentas Bancarias</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-box">
                                <th class="text-center desc">Banco</th>
                                <th class="text-center desc">Moneda</th>
                                <th class="text-center desc">Cuenta</th>
                                <th class="text-center desc">Cci</th>
                            </tr>
                            @foreach($accounts as $account)
                            <tr>
                                <td width="25%" class="text-center desc">
                                    {{$account->bank->description}}
                                </td>
                                <td width="25%" class="text-center desc">
                                    {{$account->currency_type->description}}
                                </td>
                                <td width="25%" class="text-center desc">
                                    {{$account->number}}
                                </td>
                                <td width="25%" class="text-center desc">
                                    @if($account->cci)
                                        {{$account->cci}}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </td>
        <td class="text-center">
            {{-- incio qr --}}
            <img width="15%" src="data:image/png;base64, {{ $document->qr }}" style="margin-right: -10px;" />
            {{-- fin qr --}}
        </td>
    </tr>
</table>

<table class="full-width">
    <tr>
        <td class="text-center desc font-bold">"Gracias por su preferencia"</td>
    </tr>
</table>
</body>