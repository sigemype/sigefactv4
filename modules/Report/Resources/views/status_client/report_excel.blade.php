<?php
    use App\Models\Tenant\Document;
    use App\CoreFacturalo\Helpers\Template\TemplateHelper;
    use App\Models\Tenant\SaleNote;
/* foreach ($records as $value) {
    dd($value->user->name);
} */
    dd($records);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
          content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>Document</title>
</head>
<body>
<div>
    <h3 align="center"
        class="title"><strong>Reporte Documentos</strong></h3>
</div>
<br>
<div style="margin-top:20px; margin-bottom:15px;">
    <table>
        <tr>
            <td>
                <p><b>Empresa: </b></p>
            </td>
            <td align="center">
                <p><strong>{{$company->name}}</strong></p>
            </td>
            <td>
                <p><strong>Fecha: </strong></p>
            </td>
            <td align="center">
                <p><strong>{{date('Y-m-d')}}</strong></p>
            </td>
        </tr>
        <tr>
            <td>
                <p><strong>Ruc: </strong></p>
            </td>
            <td align="center">{{$company->number}}</td>
            <td>
                <p><strong>Establecimiento: </strong></p>
            </td>
            <td align="center">{{$establishment->address}} - {{$establishment->department->description}}
                                                           - {{$establishment->district->description}}</td>
        </tr>
        @inject('reportService', 'Modules\Report\Services\ReportService')
        <tr>
            @if($filters['seller_id'])
                <td>
                    <p><strong>Usuario: </strong></p>
                </td>
                <td align="center">
                    {{$reportService->getUserName($filters['seller_id'])}}
                </td>
            @endif
            @if($filters['person_id'])
                <td>
                    <p><strong>Cliente: </strong></p>
                </td>
                <td align="center">
                    {{$reportService->getPersonName($filters['person_id'])}}
                </td>
            @endif
        </tr>
    </table>
</div>
<br>
@if(!empty($records))
    <div class="">
        <div class=" ">
            @php
                $acum_total_charges=0;
                $acum_total_taxed=0;
                $acum_total_igv=0;
                $acum_total=0;

                $serie_affec = '';
                $acum_total_exonerado=0;
                $acum_total_inafecto=0;

                $acum_total_free=0;

                $acum_total_taxed_usd = 0;
                $acum_total_igv_usd = 0;
                $acum_total_usd = 0;
            @endphp
            <table class="">
                <thead>
                <tr>
                    <th>#</th>
                    <th class="">Usuario/Vendedor</th>
                    <th>Tipo Doc</th>
                    <th>Número</th>
                    <th>Fecha emisión</th>

                    <th>Cliente</th>
                    <th>Estado</th>
                    <th class="">Moneda</th>
                    <th>Items</th>
                    <th>TC</th>
                    <th>Total Cargos</th>
                    <th>Total Exonerado</th>
                    <th>Total Inafecto</th>
                    <th>Total Gratuito</th>
                    <th>Total Gravado</th>
                    <th>Descuento total</th>
                    <th>Total IGV</th>
                    <th>Total ISC</th>
                    <th>Total</th>
                    <th>Saldo / Por pagar</th>
                    <th>Total de productos</th>
                </tr>
                </thead>
                <tbody>
                @foreach($records as $key => $value)
                    <?php
                    /** @var \App\Models\Tenant\Document|App\Models\Tenant\SaleNote  $value */
                    $iteration = $loop->iteration;
                                    $userCreator = $value->user->name;
                    $document_type = $value->getDocumentType();
                    $seller = \App\CoreFacturalo\Helpers\Template\ReportHelper::getSellerData($value);
                    try{
                        $user = $seller->name;
                    }catch (ErrorException $e){
                        $user = '';
                    }

                    ?>

                    ?>
                    <tr>
                        <td class="celda">{{$iteration}}</td>
                        <td class="celda">
                            @if($filters['user_type']==='CREADOR')
                                {{$userCreator}}
                            @else
                                {{$user}}
                            @endif
                        </td>
                        <td class="celda">{{$document_type->id}}</td>
                        <td class="celda">{{$value->series}}-{{$value->number}}</td>
                        <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>
                        <td class="celda">{{$value->person->name}}</td>
                        <td class="celda"></td>

                        @php
                            $signal = $document_type->id;
                            $state = $value->state_type_id;

                        @endphp

                        <td class="celda">{{$value->currency_type_id}}</td>
                        @php
                            $description_item='';
                            $items=[];
                            foreach ($value->items as $itm) {
                                $item=(array) $itm->item;
                                //dd($item['description']);
                                $items[]=$item['description'];
                                
                            }
                            $description_item=implode(', ', $items);
                        @endphp
                        <td>{{$description_item}}</td>
                        <td class="celda"></td>
                        <td>{{ $value->exchange_rate_sale }}</td>

                    <!-- <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_exonerated}} </td>
                                <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_unaffected}}</td>
                                <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_free}}</td>

                                <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_taxed}}</td>

                                <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_igv}}</td>
                                <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total}}</td> -->

                        @if($signal == '07')

                            @if(in_array($value->state_type_id,['09','11']))
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                                <td class="celda">0</td>
                            @else
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_charge}}</td>
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_exonerated}}</td>
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_unaffected}}</td>
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_free}}</td>
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_taxed}}</td>
                                <td class="celda">{{$value->total_discount}}</td>
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_igv}}</td>
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_isc}}</td>
                                <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total}}</td>
                            @endif

                        @else
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_charge}}</td>
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_exonerated}}</td>
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_unaffected}}</td>
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_free}}</td>

                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed}}</td>
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_discount}}</td>
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv}}</td>
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_isc}}</td>
                            <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total}}</td>

                        @endif

                        @foreach ($categories as $category)

                            @php
                                $amount = 0;
                                // dd($item->relation_item->category_id);

                                foreach ($value->items as $item) {
                                    if($item->relation_item->category_id == $category->id){
                                        $amount += $item->total;
                                    }
                                }
                            @endphp

                            <td>{{$amount}}</td>
                        @endforeach


                        @foreach ($categories_services as $category)

                            @php
                                $quantity = 0;

                                foreach ($value->items as $item) {
                                    if($item->relation_item->category_id == $category->id){
                                        $quantity += $item->quantity;
                                    }
                                }
                            @endphp

                            <td>{{$quantity}}</td>
                        @endforeach

                        @php

                            $value->total_exonerated = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_exonerated;
                            $value->total_unaffected = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_unaffected;
                            $value->total_free = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_free;

                            $value->total_taxed = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed;
                            $value->total_igv = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv;
                            $value->total = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total;
                        @endphp
                        @php
                            
                        @endphp
                        <td></td>
                        @php

                            $serie_affec =  '';

                            $quality_item=0;
                            foreach ($value->items as $itm) {
                                $quality_item+=$itm->quantity;
                            }

                        @endphp
                        <td>{{$quality_item}}</td>
                    </tr>
                    @php
                        if($value->currency_type_id == 'PEN'){
                            /*$acum_total_taxed +=  $signal != '07' ? $value->total_taxed : -$value->total_taxed ;
                            $acum_total_igv +=  $signal != '07' ? $value->total_igv : -$value->total_igv ;
                            $acum_total += $signal != '07' ? $value->total : -$value->total ;*/

                            /*$acum_total_exonerado += $signal != '07' ? $value->total_exonerated : -$value->total_exonerated ;
                            $acum_total_inafecto += $signal != '07' ? $value->total_unaffected : -$value->total_unaffected ;
                            $acum_total_free += $signal != '07' ? $value->total_free : -$value->total_free ;*/


                            if(($signal == '07' && $state !== '11')){

                                $acum_total += -$value->total;
                                $acum_total_taxed += -$value->total_taxed;
                                $acum_total_igv += -$value->total_igv;

                                $acum_total_charges += -$value->total_charge;
                                $acum_total_exonerado += -$value->total_exonerated;
                                $acum_total_inafecto += -$value->total_unaffected;
                                $acum_total_free += -$value->total_free;


                            }elseif($signal != '07' && $state == '11'){

                                $acum_total += 0;
                                $acum_total_taxed += 0;
                                $acum_total_igv += 0;

                                $acum_total_charges += 0;
                                $acum_total_exonerado += 0;
                                $acum_total_inafecto += 0;
                                $acum_total_free += 0;

                            }else{

                                $acum_total += $value->total;
                                $acum_total_taxed += $value->total_taxed;
                                $acum_total_igv += $value->total_igv;

                                $acum_total_charges += $value->total_charge;
                                $acum_total_exonerado += $value->total_exonerated;
                                $acum_total_inafecto += $value->total_unaffected;
                                $acum_total_free += $value->total_free;
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


                        }

                    @endphp
                @endforeach
                <tr>
                    <td colspan="23"></td>
                    <td colspan="2">Totales PEN</td>
                    <td>{{number_format($acum_total_charges, 2)}}</td>
                    <td>{{number_format($acum_total_exonerado, 2)}}</td>
                    <td>{{number_format ($acum_total_inafecto, 2 )}}</td>
                    <td>{{number_format($acum_total_free, 2)}}</td>
                    <td>{{$acum_total_taxed}}</td>
                    <td></td>
                    <td>{{$acum_total_igv}}</td>
                    <td></td>
                    <td>{{$acum_total}}</td>
                </tr>
                <tr>
                    <td colspan="23"></td>
                    <td colspan="2">Totales USD</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{$acum_total_taxed_usd}}</td>
                    <td></td>
                    <td>{{$acum_total_igv_usd}}</td>
                    <td></td>
                    <td>{{$acum_total_usd}}</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
@else
    <div>
        <p>No se encontraron registros.</p>
    </div>
@endif
</body>
</html>
