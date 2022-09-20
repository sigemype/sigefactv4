@php
    $establishment = $document->establishment;
    $supplier = $document->supplier;
    $payments = $document->payments;
    $tittle = $document->number ? str_pad($document->number, 8, '0', STR_PAD_LEFT) : '-';
@endphp
<html>
<head>
</head>
<body>
<table class="full-width">
    <tr>
        @if($company->logo)
            <td width="20%">
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            </td>
        @else
            <td width="20%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
        <td width="50%" class="pl-3">
            <div class="text-left">
                <h4 class="">{{ $company->name }}</h4>
                <h5>{{ 'RUC '.$company->number }}</h5>
                <h6 style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address : '' }}
                    {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                    {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                    {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                </h6>

                @isset($establishment->trade_address)
                    <h6>{{ ($establishment->trade_address !== '-')? 'D. Comercial: '.$establishment->trade_address : '' }}</h6>
                @endisset
                <h6>{{ ($establishment->telephone !== '-')? 'Central telefónica: '.$establishment->telephone : '' }}</h6>

                <h6>{{ ($establishment->email !== '-')? 'Email: '.$establishment->email : '' }}</h6>

                @isset($establishment->web_address)
                    <h6>{{ ($establishment->web_address !== '-')? 'Web: '.$establishment->web_address : '' }}</h6>
                @endisset

                @isset($establishment->aditional_information)
                    <h6>{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</h6>
                @endisset
            </div>
        </td>
        <td width="30%" class="border-box py-4 px-2 text-center">
            <h5 class="text-center">{{ $document->expense_type->description}}</h5>
            <h3 class="text-center">{{ $tittle }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-5">
    <tr>
        <td width="15%">Proveedor:</td>
        <td width="45%">{{ $supplier->name }}</td>
        <td width="15%">Fecha de emisión:</td>
        <td >{{ $document->date_of_issue->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td>{{ $supplier->identity_document_type->description }}:</td>
        <td>{{ $supplier->number }}</td>

        @if ($supplier->telephone)
        <tr>
            <td width="25%">Teléfono:</td>
            <td>
                {{ $supplier->telephone }}
            </td>
        </tr>
        @endif
    
    @if ($supplier->address)
    <tr>
        <td class="align-top">Dirección:</td>
        <td colspan="3">
            {{ $supplier->address }}
            {{ ($supplier->district_id !== '-')? ', '.$supplier->district->description : '' }}
            {{ ($supplier->province_id !== '-')? ', '.$supplier->province->description : '' }}
            {{ ($supplier->department_id !== '-')? '- '.$supplier->department->description : '' }}
        </td>
    </tr>
    @endif

    <tr>
        <td width="15%">Usuario:</td>
        <td width="45%"> {{ $document->user->name }}</td>
        <td width="15%">Motivo:</td>
        <td>{{ $document->expense_reason->description }}</td>
    </tr>
</table>


<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2" width="5%">#</th>
        <th class="border-top-bottom text-left py-2">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-right py-2" width="12%">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center">
                {{ $loop->iteration }}
            </td>
            <td class="text-left">
                {!!$row->description!!}
            </td>
            <td class="text-right align-top">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="3" class="border-bottom"></td>
        </tr>
    @endforeach
        <tr>
            <td colspan="2" class="text-right font-bold">TOTAL: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
</table>


@if($payments->count())
    <table class="full-width">
        <tr>
            <td>
                <strong>Distribución de gasto:</strong>
            </td>
        </tr>
            @php
                $payment = 0;
            @endphp
            @foreach($payments as $row)
                <tr>
                    <td>&#8226; {{ $row->expense_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment }}</td>
                </tr>
            @endforeach
        </tr>

    </table>
@endif


</body>
</html>
