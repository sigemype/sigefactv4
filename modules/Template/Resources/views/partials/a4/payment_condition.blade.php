{{--@php--}}
{{--    if($document->payment_condition_id === '01') {--}}
{{--        $paymentCondition = \App\Models\Tenant\PaymentMethodType::where('id', '10')->first();--}}
{{--    }else{--}}
{{--        $paymentCondition = \App\Models\Tenant\PaymentMethodType::where('id', '09')->first();--}}
{{--    }--}}
{{--@endphp--}}
{{-- Condicion de pago  Crédito / Contado --}}
{{--<table class="full-width">--}}
{{--    <tr>--}}
{{--        <td>--}}
{{--            <strong>CONDICIÓN DE PAGO: {{ $paymentCondition->description }} </strong>--}}
{{--        </td>--}}
{{--    </tr>--}}
{{--</table>--}}

@if($document->payment_method_type_id)
    <table class="full-width">
        <tr>
            <td>
                <strong>MÉTODO DE PAGO: </strong>{{ $document->payment_method_type->description }}
            </td>
        </tr>
    </table>
@endif
@if ($document->payment_condition_id === '01')
    @if($document->payments->count())
        <table class="full-width">
            <tr>
                <td><strong>PAGOS:</strong></td>
            </tr>
            @php $payment = 0; @endphp
            @foreach($document->payments as $row)
                <tr>
                    <td>&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@else
    <table class="full-width">
        @foreach($document->fee as $key => $quote)
            <tr>
                <td>&#8226; {{ (empty($quote->getStringPaymentMethodType()) ? 'Cuota #'.( $key + 1) : $quote->getStringPaymentMethodType()) }} /
                    Fecha Vencimiento: {{ (new \App\CoreFacturalo\HelperFacturalo())->date_of_issue_format($quote->date) }} / Monto: {{ $quote->currency_type->symbol }} {{ $quote->amount }}</td>
            </tr>
        @endforeach
    </table>
@endif
