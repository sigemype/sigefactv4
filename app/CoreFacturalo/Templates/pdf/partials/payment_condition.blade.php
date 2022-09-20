 
<!--
//    <?php
//@if ($document->payment_condition_id === '01')
//    @if($payments->count())
//        <table class="full-width">
//            <tr>
//                <td><strong>PAGOS:</strong></td>
//            </tr>
//            @php $payment = 0; @endphp
//            @foreach($payments as $row)
//                <tr>
//                    <td>&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td>
//                </tr>
//                @endforeach
//                </tr>
//        </table>
//    @endif
//@else
//    <table class="full-width">
//        @foreach($document->fee as $key => $quote)
//            <tr>
//                <td>&#8226; {{ (empty($quote->getStringPaymentMethodType()) ? 'Cuota #'.( $key + 1) : $quote->getStringPaymentMethodType()) }} / Fecha vencimiento: {{ $quote->date->format('d-m-Y') }} / Monto: {{ $quote->currency_type->symbol }}{{ $quote->amount }}</td>
//            </tr>
//            @endforeach
//            </tr>
//    </table>
//@endif
//?> -->
