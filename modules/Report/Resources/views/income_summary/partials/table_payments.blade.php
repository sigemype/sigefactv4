
@php
    $data_payments = $cash->getIncomePaymentsData();
@endphp

@if($cash_documents->count())

    <div class="">
        <div class=" ">
            <table class="">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha y hora emisión</th>
                        <th>Tipo documento</th>
                        <th>Documento</th>
                        <th>Método de pago</th> 
                        <th>Moneda</th>
                        <th>Importe</th>
                        <th>Vuelto</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- comprobantes --}}
                    @foreach($data_payments['documents'] as $value)
                        @php

                            $type_transaction =  null;
                            $document_type_description = null;
                            $number = null;
                            $date_time_of_issue = null;
                            $payment_method_description = null;
                            $total = null;  
                            $currency_type_id = null;
                        @endphp
                        
                        @foreach($value->document->payments as $payment)
                        <tr>
                            @php
                                $type_transaction =  'Venta';
                                $document_type_description =  $value->document->document_type->description;
                                $number = $value->document->number_full;
                                $date_time_of_issue = "{$value->document->date_of_issue->format('Y-m-d')} {$value->document->time_of_issue}";
                                $payment_method_description = $payment->payment_method_type->description;
                                $total = $payment->payment;
                                
                                if(!in_array($payment->associated_record_payment->state_type_id, ['01','03','05','07','13'])){
                                    $total = 0;
                                }
                                
                                $currency_type_id = $value->document->currency_type_id;

                            @endphp


                            <td class="celda">{{ $loop->iteration }}</td>
                            <td class="celda">{{ $date_time_of_issue}}</td>
                            <td class="celda">{{ $document_type_description }}</td>
                            <td class="celda">{{ $number }}</td>
                            <td class="celda">{{$payment_method_description }}</td>  
                            <td class="celda">{{$currency_type_id }}</td>  
                            <td class="celda">{{ number_format($value->document->total,2) }}</td>
                            <td class="celda">{{ number_format($payment->change,2) }}</td>
                            <td class="celda">{{ number_format($total,2) }}</td>

                        </tr>
                        @endforeach
                    @endforeach
                    {{-- comprobantes --}}


                    {{-- notas de venta --}}
                    @foreach($data_payments['sale_notes'] as $value)

                        @php
                            $type_transaction =  null;
                            $document_type_description = null;
                            $number = null;
                            $date_time_of_issue = null;
                            $payment_method_description = null;
                            $total = null;  
                            $currency_type_id = null;
                        @endphp

                        @foreach($value->sale_note->payments as $payment)
                        <tr>
                            
                            @php
                                $type_transaction =  'Venta';
                                $document_type_description =  'NOTA DE VENTA';
                                $number = $value->sale_note->number_full;
                                $date_time_of_issue = "{$value->sale_note->date_of_issue->format('Y-m-d')} {$value->sale_note->time_of_issue}";
                                $payment_method_description = $payment->payment_method_type->description;
                                $total = $payment->payment;
                                
                                if(!in_array($payment->associated_record_payment->state_type_id, ['01','03','05','07','13'])){
                                    $total = 0;
                                }

                                $currency_type_id = $value->sale_note->currency_type_id;

                            @endphp


                            <td class="celda">{{ $loop->iteration }}</td>
                            <td class="celda">{{ $date_time_of_issue}}</td>
                            <td class="celda">{{ $document_type_description }}</td>
                            <td class="celda">{{ $number }}</td>
                            <td class="celda">{{$payment_method_description }}</td>  
                            <td class="celda">{{$currency_type_id }}</td>  
                            <td class="celda">{{ number_format($value->sale_note->total,2) }}</td>
                            <td class="celda">{{ number_format($payment->change,2) }}</td>
                            <td class="celda">{{ number_format($total,2) }}</td>

                        </tr>
                        @endforeach
                    @endforeach
                    {{-- notas de venta --}}


                    {{-- comprobantes y notas de venta a credito --}}
                    @foreach($cash_documents_credit as $value)
                                    
                        @php
                            $type_transaction =  null;
                            $document_type_description = null;
                            $number = null;
                            $date_time_of_issue = null;
                        
                            $total = null;  
                            $currency_type_id = null;
                        @endphp

                        @if($value->sale_note)

                            <tr>
                                @php
                                    $document = $value->sale_note;
                                    $type_transaction =  'Venta';
                                    $document_type_description =  'NOTA DE VENTA';
                                    $number = $document->number_full;
                                    $date_time_of_issue = "{$document->date_of_issue->format('Y-m-d')} {$document->time_of_issue}";
                                    $payment_method_description = 'Crédito';
                                    $total = 0;
                                    
                                    $currency_type_id = $document->currency_type_id;
                                @endphp

                                <td class="celda">{{ $loop->iteration }}</td>
                                <td class="celda">{{ $date_time_of_issue}}</td>
                                <td class="celda">{{ $document_type_description }}</td>
                                <td class="celda">{{ $number }}</td>
                                <td class="celda">{{$payment_method_description }}</td>  
                                <td class="celda">{{$currency_type_id }}</td>  
                                <td class="celda">{{ number_format($document->total,2) }}</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>

                            </tr>

                        @elseif($value->document)
                        
                            <tr>
                                @php
                                    $document = $value->document;
                                    $type_transaction =  'Venta';
                                    $document_type_description =  $document->document_type->description;
                                    $number = $document->number_full;
                                    $date_time_of_issue = "{$document->date_of_issue->format('Y-m-d')} {$document->time_of_issue}";
                                    $payment_method_description = 'Crédito';
                                    $currency_type_id = $document->currency_type_id;

                                @endphp

                                <td class="celda">{{ $loop->iteration }}</td>
                                <td class="celda">{{ $date_time_of_issue}}</td>
                                <td class="celda">{{ $document_type_description }}</td>
                                <td class="celda">{{ $number }}</td>
                                <td class="celda">{{$payment_method_description }}</td>  
                                <td class="celda">{{$currency_type_id }}</td>  
                                <td class="celda">{{ number_format($document->total,2) }}</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                            </tr>
                        @endif
                    @endforeach
                    {{-- comprobantes y notas de venta a credito --}}

                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="callout callout-info">
        <p>No se encontraron registros.</p>
    </div>
@endif