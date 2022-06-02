<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Reporte de CPE</title>
        <style>
            @page {
              margin: 8;
            }
            html {
                font-family: sans-serif;
                font-size: 11px;
            }
            table {
                width: 100%;
                border-spacing: 0;
                border: 1px solid black;
            }
            .celda {
                text-align: center;
                padding: 5px;
                border: 0.1px solid black;
            }
            th {
                padding: 5px;
                text-align: center;
                border-color: #0088cc;
                border: 0.1px solid black;
            }
            .title {
                font-weight: bold;
                padding-bottom: 5px;
                font-size: 20px !important;
                text-decoration: underline;
            }
            p>strong {
                margin-left: 5px;
                font-size: 13px;
            }
            thead {
                font-weight: bold;
                background: #0088cc;
                color: white;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div>
            <p align="center" class="title"><strong>Reporte de Cpe's al {{ $date_now }}</strong></p>
        </div>
        {{-- <div style="margin-top:20px; margin-bottom:20px;">
        </div> --}}
        @if($records->count())
            <div class="">
                <div class=" ">
                    @php
                        $acum_total_taxed=0;
                        $acum_total_igv=0;
                        $acum_total=0;

                        $color = '';

                        $serie_affec = '';

                        $acum_total_exonerado=0;
                        $acum_total_inafecto=0;

                        $acum_total_free=0;

                        $acum_total_taxed_usd=0;
                        $acum_total_igv_usd=0;
                        $acum_total_usd=0;

                        $acum_total_taxed_eur=0;
                        $acum_total_igv_eur=0;
                        $acum_total_eur=0;
                    @endphp
                    <table class="" style="font-size:10px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo Doc</th>
                                <th>Número</th>
                                <th>Fecha emisión</th>
                                <th>Fecha Vencimiento</th>
                                <th>Doc. Afectado</th>
                                <th># Guía</th>
                                <th>RUC</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Forma de Pago</th>
                                <th class="">Moneda</th>
                                {{-- <th>Plataforma</th> --}}
                                {{-- <th>Orden de compra</th> --}}
                                <!-- <th>Total Exonerado</th>
                                <th>Total Inafecto</th>
                                    <th>Total Gratutio</th> -->
                                <th>Total Gravado</th>
                                <th>Total IGV</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($records as $key => $value)
                                @if(in_array($value->state_type->id,["11","09"]))
                                    @php
                                        $color = "red";
                                    @endphp
                                @elseif(in_array($value->state_type->id,["01"]))
                                    @php
                                        $color = "blue";
                                    @endphp
                                @endif
                                <tr style="color: {{ $color }}">
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value->document_type->id}}</td>
                                    <td class="celda">{{$value->series}}-{{$value->number}}</td>
                                    <td class="celda">{{$value->date_of_issue->format('d/m/Y')}}</td>
                                    <td class="celda">{{isset($value->invoice)?$value->invoice->date_of_due->format('d/m/Y'):''}}</td>
                                    @if(in_array($value->document_type_id,["07","08"]) && $value->note)
                                        @php
                                            $serie = ($value->note->affected_document) ? $value->note->affected_document->series : $value->note->data_affected_document->series;
                                            $number =  ($value->note->affected_document) ? $value->note->affected_document->number : $value->note->data_affected_document->number;
                                            $serie_affec = $serie.' - '.$number;
                                        @endphp
                                    @endif
                                    <td class="celda">{{  $serie_affec }} </td>
                                    <td class="celda">
                                        @if(!empty($value->guides))
                                            @foreach($value->guides as $guide)
                                                {{ $guide->number }}<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="celda">{{$value->customer->number}}</td>
                                    <td class="celda">{{$value->customer->name}}</td>
                                    <td class="celda">{{$value->state_type->description}}</td>
                                    <td class="celda">
                                        @php
                                            $fees = $value->fee;
                                            $cantfees = $fees->count();
                                        @endphp
                                        @if ($fees->count())
                                            Credito
                                        @else
                                            Contado
                                        @endif
                                    </td>
                                    <td class="celda">{{$value->currency_type_id}}</td>
                                    @php
                                     $signal = $value->document_type_id;
                                     $state = $value->state_type_id;
                                    @endphp
                                    @if($signal == '07')
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_taxed}}</td>
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_igv}}</td>
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total}}</td>
                                    @else
                                        <td class="celda">{{ (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed}}</td>
                                        <td class="celda">{{ (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv}}</td>
                                        <td class="celda">{{ (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total}}</td>
                                    @endif
                                    @php
                                        $value->total_taxed = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed;
                                        $value->total_igv = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv;
                                        $value->total = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total;
                                    @endphp
                                </tr>
                                @php
                                    $serie_affec =  '';
                                    $color = '';
                                @endphp
                                @php
                                    if($value->currency_type_id == 'PEN'){
                                        if(($signal == '07' && $state !== '11')){
                                            $acum_total += -$value->total;
                                            $acum_total_taxed += -$value->total_taxed;
                                            $acum_total_igv += -$value->total_igv;
                                        }elseif($signal != '07' && $state == '11'){
                                            $acum_total += 0;
                                            $acum_total_taxed += 0;
                                            $acum_total_igv += 0;
                                        }else{
                                            $acum_total += $value->total;
                                            $acum_total_taxed += $value->total_taxed;
                                            $acum_total_igv += $value->total_igv;
                                        }
                                    }else if($value->currency_type_id == 'USD'){
                                        if(($signal == '07' && $state !== '11')){
                                            $acum_total_usd += -$value->total;
                                            $acum_total_taxed_usd += -$value->total_taxed;
                                            $acum_total_igv_usd += -$value->total_igv;
                                        }elseif($signal != '07' && $state == '11'){
                                            $acum_total_usd += 0;
                                            $acum_total_taxed_usd += 0;
                                            $acum_total_igv_usd += 0;
                                        }else{
                                            $acum_total_usd += $value->total;
                                            $acum_total_taxed_usd += $value->total_taxed;
                                            $acum_total_igv_usd += $value->total_igv;
                                        }
                                    }else if($value->currency_type_id == 'EUR'){
                                        if(($signal == '07' && $state !== '11')){
                                            $acum_total_eur += -$value->total;
                                            $acum_total_taxed_eur += -$value->total_taxed;
                                            $acum_total_igv_eur += -$value->total_igv;
                                        }elseif($signal != '07' && $state == '11'){
                                            $acum_total_eur += 0;
                                            $acum_total_taxed_eur += 0;
                                            $acum_total_igv_eur += 0;
                                        }else{
                                            $acum_total_eur += $value->total;
                                            $acum_total_taxed_eur += $value->total_taxed;
                                            $acum_total_igv_eur += $value->total_igv;
                                        }
                                    }
                                @endphp
                            @endforeach
                            <tr>
                                <td class="celda" colspan="11"></td>
                                <td class="celda" >Totales PEN</td>
                                <td class="celda">{{$acum_total_taxed}}</td>
                                <td class="celda">{{$acum_total_igv}}</td>
                                <td class="celda">{{$acum_total}}</td>
                            </tr>
                            <tr>
                                <td class="celda" colspan="11"></td>
                                <td class="celda" >Totales USD</td>
                                <td class="celda">{{$acum_total_taxed_usd}}</td>
                                <td class="celda">{{$acum_total_igv_usd}}</td>
                                <td class="celda">{{$acum_total_usd}}</td>
                            </tr>
                            <tr>
                                <td class="celda" colspan="11"></td>
                                <td class="celda" >Totales EUR</td>
                                <td class="celda">{{$acum_total_taxed_eur}}</td>
                                <td class="celda">{{$acum_total_igv_eur}}</td>
                                <td class="celda">{{$acum_total_eur}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="callout callout-info">
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
