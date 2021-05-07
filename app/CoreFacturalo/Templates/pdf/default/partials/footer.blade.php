@php
    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $accounts = \App\Models\Tenant\BankAccount::where('show_in_documents', true)->get();
@endphp
<head>
    <link href="{{ $path_style }}" rel="stylesheet" />
</head>
<body>
@if(in_array($document->document_type->id,['01','03']))
    @if ($accounts != "[]")
        <table class="full-width">
            <thead>
                <tr>
                    <th colspan="4">
                        <strong>Cuentas Bancarias</strong>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-box">
                    <th class="text-center">Banco</th>
                    <th class="text-center">Moneda</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Cci</th>
                </tr>
                @foreach($accounts as $account)
                <tr>
                    <td width="25%" class="text-center">
                        {{$account->bank->description}}
                    </td>
                    <td width="25%" class="text-center">
                        {{$account->currency_type->description}}
                    </td>
                    <td width="25%" class="text-center">
                        {{$account->number}}
                    </td>
                    <td width="25%" class="text-center">
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
<br>
<table class="full-width">
    <tr>
        <td class="text-center desc font-bold">"Gracias por su preferencia"</td>
    </tr>
    <tr>
        <td class="text-center desc font-bold">Para consultar el comprobante ingresar a {!! url('/buscar') !!}</td>
    </tr>
</table>
</body>