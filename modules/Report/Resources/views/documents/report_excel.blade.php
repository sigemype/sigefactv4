<?php
    use App\Models\Tenant\Document;
    use App\CoreFacturalo\Helpers\Template\TemplateHelper;
    use App\Models\Tenant\SaleNote;
    use App\Models\Tenant\Catalogs\DocumentType;
    use App\Models\Tenant\Series;

    $enabled_sales_agents = App\Models\Tenant\Configuration::getRecordIndividualColumn('enabled_sales_agents');

$col_num=6;
    //dd($columns);
foreach ($columns as $value) {
    switch ($value->title) {
        case 'Opciones':
        case 'Total':
        case 'Total IGV':
        case 'Total Gratuito':
        case 'Total Gravado':
        case 'Total Exonerado':
        case 'Total Inafecto':
        case 'Productos':
        case 'Total Cargos':
            $col_num=$col_num;
            break;
        case 'Total ISC':
            $col_num+=1;
            break;
        case 'Total Cargos':
            $col_num+=1;
            break;
        
        default:
            if ($value->visible) {
                $col_num+=1;
            }
            break;
    }
}
//dd(count($records));
$document_types=DocumentType::OnlyAvaibleDocuments()->get();



//dd($col_num);
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
                

                $acum_documents=[];
                $acum_series=[];
                $clear_type=[];
                $clear_series=[];
                foreach ($records as $key => $value) {
                    $document_type = $value->getDocumentType();
                    $clear_type[] = $document_type->id;
                    $clear_series[] = $value->series;
                    
                }
                $clear_type=array_unique($clear_type);
                $clear_series=array_unique($clear_series);
                $clear_type=array_values($clear_type);
                $clear_series=array_values($clear_series);
                
            @endphp
            {{-- @foreach($document_types as $type) --}}
            
            @for ($i = 0; $i < count($clear_type); $i++)
                @for ($c = 0; $c < count($document_types); $c++)
                    @if ($document_types[$c]->id==$clear_type[$i])
                        @php
                            $acum_documents[]=[
                                'id'=>$document_types[$c],
                                'description'=>$document_types[$c]->description,
                            ];
                            $series_document=Series::FilterDocumentType($document_types[$c]->id)->select('number')->get();
                            //dd($document_types[$c]->id==$clear_type[$i]);
                            $title=$document_types[$c]->description;
                            //dd($series_document);
                        @endphp
                        @for ($cs = 0; $cs < count($clear_series); $cs++)
                        @for ($s = 0; $s < count($series_document); $s++)
                            @php
                                $serie_type=$series_document[$s];
                                //dd($serie_type['number']==$clear_series[$cs]);
                            @endphp
                            @if ($serie_type['number']==$clear_series[$cs])
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
                                    //dd($serie_type['number']);
                                    $serie_number=$serie_type['number'];
                                @endphp
                <h3>{{$title}} - {{$serie_number}}</h3>
                <table class="">
                    <thead>
                    <tr>
                        <th>#</th>
                        @if ($columns->user_seller->visible)
                            <th class="">Usuario/Vendedor</th>
                        @endif
                        
                        <th>Tipo Doc</th>
                        <th>Serie</th>
                        <th>Número</th>
                        <th>Fecha emisión</th>
                        <th>Fecha Vencimiento</th>
                        @if ($columns->doc_affect->visible)
                            <th>Doc. Afectado</th>
                        @endif
                        
                        @if ($columns->guides->visible)
                            <th># Guía</th>
                        @endif
                        @if ($columns->quote->visible)
                            <th>Cotización</th>
                        @endif
                        @if ($columns->case->visible)
                            <th>Caso</th>
                        @endif
                        
                        @if ($columns->district->visible)
                        <th>DIST</th>
                        @endif
                        @if ($columns->department->visible)
                        <th>DPTO</th>
                        @endif
                        @if ($columns->province->visible)
                        <th>PROV</th>
                        @endif
                        @if ($columns->client_direction->visible)
                        <th>Direccion de cliente</th>
                        @endif
                        <th>Cliente</th>
                        @if ($columns->ruc->visible)
                        <th>RUC</th>
                        @endif
                        
                        <th>Estado</th>
                        @if ($columns->currency_type_id->visible)
                            <th class="">Moneda</th>
                        @endif
                        
                        @if ($columns->web_platforms->visible)
                        <th>Plataforma</th>
                        @endif
                        @if ($columns->purchase_order->visible)
                            <th>Orden de compra</th>
                        @endif
                        
                        @if ($columns->note_sale->visible)
                        <th>Nota de venta</th>
                        @endif
                        @if ($columns->date_note->visible)
                        <th>Fecha N. Venta</th>
                        @endif
                        @if ($columns->payment_form->visible)
                        <th class="">Forma de pago</th>
                        @endif
                        @if ($columns->payment_method->visible)
                        <th> MÉTODO DE PAGO </th>
                        @endif
                        @if ($columns->total_charge->visible)
                        <th>Total Cargos</th>
                        @endif
                        @if ($columns->total_exonerated->visible)
                            <th>Total Exonerado</th>
                        @endif
                        @if ($columns->total_unaffected->visible)
                            <th>Total Inafecto</th>
                        @endif
                        @if ($columns->total_free->visible)
                            <th>Total Gratuito</th>
                        @endif
                        @if ($columns->total_taxed->visible)
                            <th>Total Gravado</th>
                        @endif
                        
                        <th>Descuento total</th>
                        @if ($columns->total_igv->visible)
                            <th>Total IGV</th>
                        @endif
                        
                        @if ($columns->total_isc->visible)
                        <th>Total ISC</th>
                        @endif
                        @if ($columns->total->visible)
                            <th>Total</th>
                        @endif
                        
                        @if ($columns->items->visible)
                            <th>Total de productos</th>
                        @endif
                        
    
                        @foreach ($categories as $category)
                            <th>{{$category->name}}</th>
                        @endforeach
    
                        @foreach ($categories_services as $category)
                            <th>{{$category->name}}</th>
                        @endforeach

                        <th>TC</th>

                        @if ($enabled_sales_agents)
                            <th>Agente</th>
                            <th>Datos de referencia</th>
                        @endif

                    </tr>
                    </thead>
                    <tbody>
                    {{-- @foreach($records as $key => $value) --}}
                    @for ($t = 0; $t < count($records); $t++)
                        
                        <?php
                        $value=$records[$t];
                        /** @var \App\Models\Tenant\Document|App\Models\Tenant\SaleNote  $value */
                        //$iteration = $loop->iteration;
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
                        @if ($document_types[$c]->id==$document_type->id)
                        @if ($serie_type['number']==$value->series)
                        <tr>
                            <td class="celda">{{$t+1}}</td>
                            @if ($columns->user_seller->visible)
                            <td class="celda">
                                @if($filters['user_type']==='CREADOR')
                                    {{$userCreator}}
                                @else
                                    {{$user}}
                                @endif
                            </td>
                            @endif
                            <td class="celda">{{$document_type->id}}</td>
                            <td class="celda">{{$value->series}}</td>
                            <td class="celda">{{$value->number}}</td>
                            <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>
                            <td class="celda">{{isset($value->invoice) ? $value->invoice->date_of_due->format('Y-m-d'):''}}</td>
                            @if(in_array($document_type->id,["07","08"]) && $value->note)
    
                                @php
                                    $serie = ($value->note->affected_document) ? $value->note->affected_document->series : $value->note->data_affected_document->series;
                                    $number =  ($value->note->affected_document) ? $value->note->affected_document->number : $value->note->data_affected_document->number;
                                    $serie_affec = $serie.' - '.$number;
    
                                @endphp
    
    
                            @endif
                            @if ($columns->doc_affect->visible)
                                <td class="celda">{{$serie_affec }} </td>
                            @endif
                            
                            @if ($columns->guides->visible)
                            <td class="celda">
                                @if(!empty($value->guides))
                                    @foreach($value->guides as $guide)
                                        {{ $guide->number }}<br>
                                    @endforeach
                                @endif
                            </td>
                            @endif
                            @if ($columns->quote->visible)
                                <td class="celda">{{ ($value->quotation) ? $value->quotation->number_full : '' }}</td>
                            @endif
                            @if ($columns->case->visible)
                                <td class="celda">{{ isset($value->quotation->sale_opportunity) ? $value->quotation->sale_opportunity->number_full : '' }}</td>
                            @endif
                            
    
                            <?php $stablihsment = \App\CoreFacturalo\Helpers\Template\ReportHelper::getLocationData($value); ?>
                            @if ($columns->district->visible)
                            <td class="celda">{{$stablihsment['district']}}</td>
                            @endif
                            @if ($columns->department->visible)
                            <td class="celda">{{$stablihsment['department']}}</td>
                            @endif
                            @if ($columns->province->visible)
                            <td class="celda">{{$stablihsment['province']}}</td>
                            @endif
                            @if ($columns->client_direction->visible)
                            <td class="celda">{{$value->customer->address}}</td>
                            @endif
                            <td class="celda">{{$value->customer->name}}</td>
                            @if ($columns->ruc->visible)
                            <td class="celda">{{$value->customer->number}}</td>
                            @endif
                            <td class="celda">{{$value->state_type->description}}</td>
    
                            @php
                                $signal = $document_type->id;
                                $state = $value->state_type_id;
                            @endphp
                            @if ($columns->currency_type_id->visible)
                                <td class="celda">{{$value->currency_type_id}}</td>
                            @endif
                            
                            @if ($columns->web_platforms->visible)
                            <td class="celda">
                                @foreach ($value->getPlatformThroughItems() as $platform)
                                    <label class="d-block">{{$platform->name}}</label>
                                @endforeach
                            </td>
                            @endif
                            @if ($columns->purchase_order->visible)
                                <td class="celda">{{$value->purchase_order}}</td>
                            @endif
                            
    
                            @if($value->sale_note)
                                @if ($columns->note_sale->visible)
                                <td class="celda">{{ $value->sale_note->number_full }}</td>
                                @endif
                                @if ($columns->date_note->visible)
                                <td class="celda">{{ $value->sale_note->date_of_issue->format('Y-m-d') }}</td>
                                @endif
                            @else
                                @if ($columns->note_sale->visible)
                                <td class="celda"></td>
                                @endif
                                @if ($columns->date_note->visible)
                                <td class="celda"></td>
                                @endif
                            @endif
                            @if ($columns->payment_form->visible)
                            <td class="celda">
                                {{ ($value->payments()->count() > 0) ? $value->payments()->first()->payment_method_type->description : ''}}
                            </td>
                            @endif
                            @if ($columns->payment_method->visible)
                            <td class="celda">
                                @php
                                $payments= [];
                                if(
                                    get_class($value) == Document::class ||
                                    get_class($value) == SaleNote::class
                                ){
                                    $payments = TemplateHelper::getDetailedPayment($value);
                                }
                                @endphp
    
                                @foreach ($payments as $payment)
                                    @foreach ($payment as $pay)
                                        {{ $pay['description'] }}
                                        @if ($loop->count > 1 && !$loop->last)
                                            <br>
                                        @endif
                                    @endforeach
                                @endforeach
    
                            </td>
                            @endif
    
                        <!-- <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_exonerated}} </td>
                                    <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_unaffected}}</td>
                                    <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_free}}</td>
    
                                    <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_taxed}}</td>
    
                                    <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_igv}}</td>
                                    <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total}}</td> -->
    
                            @if($signal == '07')
    
                                @if(in_array($value->state_type_id,['09','11']))
                                    @if ($columns->total_charge->visible)
                                        <td class="celda">0</td>
                                    @endif
                                        <td class="celda">0</td>
                                        <td class="celda">0</td>
                                        <td class="celda">0</td>
                                        <td class="celda">0</td>
                                        <td class="celda">0</td>
                                        <td class="celda">0</td>
                                        @if ($columns->total_isc->visible)
                                        <td class="celda">0</td>
                                        @endif
                                        <td class="celda">0</td>
                                    
                                @else
                                    @if ($columns->total_charge->visible)
                                    <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_charge}}</td>
                                    @endif
                                    @if ($columns->total_exonerated->visible)
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_exonerated}}</td>
                                    @endif
                                    @if ($columns->total_unaffected->visible)
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_unaffected}}</td>
                                    @endif
                                    @if ($columns->total_free->visible)
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_free}}</td>
                                    @endif
                                    @if ($columns->total_taxed->visible)
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_taxed}}</td>
                                    @endif
                                    
                                    <td class="celda">{{$value->total_discount}}</td>
                                    @if ($columns->total_igv->visible)
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_igv}}</td>
                                    @endif
                                    
                                    @if ($columns->total_isc->visible)
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_isc}}</td>
                                    @endif
                                    @if ($columns->total->visible)
                                        <td class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total}}</td>
                                    @endif
                                    
                                @endif
    
                            @else
                                @if ($columns->total_charge->visible)
                                    <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_charge}}</td>
                                @endif
                                @if ($columns->total_exonerated->visible)
                                    <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_exonerated}}</td>
                                @endif
                                
                                @if ($columns->total_unaffected->visible)
                                    <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_unaffected}}</td>
                                @endif
                                
                                @if ($columns->total_free->visible)
                                    <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_free}}</td>
                                @endif
                                
                                @if ($columns->total_taxed->visible)
                                    <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed}}</td>
                                @endif
                                
                                <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_discount}}</td>
                                @if ($columns->total_igv->visible)
                                    <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv}}</td>
                                @endif
                                
                                @if ($columns->total_isc->visible)
                                <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_isc}}</td>
                                @endif
                                @if ($columns->total->visible)
                                    <td class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total}}</td>
                                @endif
                                
    
                            @endif
    
                            @php
    
                                $serie_affec =  '';
    
                                $quality_item=0;
                                foreach ($value->items as $itm) {
                                    $quality_item+=$itm->quantity;
                                }
    
                            @endphp

                            @if ($columns->items->visible)
                                <td>{{$quality_item}}</td>
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
    

                            <td>{{ $value->exchange_rate_sale }}</td>

                            @if ($enabled_sales_agents)
                                <td>{{optional($value->agent)->search_description}}</td>
                                <td>{{$value->reference_data}}</td>
                            @endif

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
                        
                        @endif
                        @endif
                    @endfor
                    <tr>
                        @php
                            //$acum_series=$acum_total;
                            //array_push($acum_series,['total'=>$acum_total]);
                        @endphp
                        <td colspan="{{$col_num}}"></td>
                    <!-- <td >Totales</td>
                                    <td>{{$acum_total_exonerado}}</td>
                                    <td>{{$acum_total_inafecto}}</td>
                                    <td>{{$acum_total_free}}</td> -->
                        <td>Totales PEN</td>
                        @if ($columns->total_charge->visible)
                        <td>{{number_format($acum_total_charges, 2)}}</td>
                        @endif
                        @if ($columns->total_exonerated->visible)
                        <td>{{number_format($acum_total_exonerado, 2)}}</td>
                        @endif
                        
                        @if ($columns->total_unaffected->visible)
                        <td>{{number_format ($acum_total_inafecto, 2 )}}</td>
                        @endif
                        
                        @if ($columns->total_free->visible)
                        <td>{{number_format($acum_total_free, 2)}}</td>
                        @endif
                        
                        @if ($columns->total_taxed->visible)
                        <td>{{$acum_total_taxed}}</td>
                        @endif
                        
                        <td></td>
                        @if ($columns->total_igv->visible)
                        <td>{{$acum_total_igv}}</td>
                        @endif
                        
                        @if ($columns->total_isc->visible)
                        <td></td>
                        @endif
                        @if ($columns->total->visible)
                        <td>{{$acum_total}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="{{$col_num}}"></td>
                        <td>Totales USD</td>
                        @if ($columns->total_charge->visible)
                        <td></td>
                        @endif
                        
                        @if ($columns->total_exonerated->visible)
                        <td></td>
                        @endif
                        @if ($columns->total_unaffected->visible)
                        <td></td>
                        @endif
                        @if ($columns->total_free->visible)
                        <td></td>
                        @endif
                        @if ($columns->total_taxed->visible)
                        <td>{{$acum_total_taxed_usd}}</td>
                        @endif
                        
                        <td></td>
                        @if ($columns->total_igv->visible)
                        <td>{{$acum_total_igv_usd}}</td>
                        @endif
                        
                        @if ($columns->total_isc->visible)
                        <td></td>
                        @endif
                        @if ($columns->total->visible)
                        <td>{{$acum_total_usd}}</td>
                        @endif
                        
                    </tr>
    
                    </tbody>
                </table>
                @php
                $acum_series[]=[
                    'document_id'=>$document_types[$c],
                    'number' =>$serie_type->number,
                    'total' => $acum_total,
                ];
            @endphp     
                        @endif{{-- DOCUMENTOS SEGUN ID SERIE --}}
                        @endfor{{-- SERIES TOTALES --}}
                        @endfor{{-- SERIES TOTALES --}}
                        
                    @endif{{-- IGUALDAD DE ID --}}
                @endfor{{-- LOS DOCUMENTOS --}}
            @endfor{{-- TIPOS DE DOCUMENTO --}}
            <h3>TOTAL POR SERIE Y DOCUMENTO</h3>
            @php
                //dd($acum_series);
            @endphp
            <table>
                <thead>
                    <tr>
                        <th>DOC</th>
                        <th class="">SERIE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                       $total_general=0;
                    @endphp
                    @foreach ($acum_documents as $document)
                    
                        @foreach ($acum_series as $serie)
                            @if ($document['id']==$serie['document_id'])
                                <tr>
                                    <td>{{$document['description']}}</td>
                                    <td>{{$serie['number']}}</td>
                                    <td>{{$serie['total']}}</td>
                                </tr>
                                @php
                                    $total_general=$total_general+$serie['total'];
                                @endphp
                            @endif
                        @endforeach
                    @endforeach
                    <tr >
                        <td colspan="2">TOTAL GENERAL</td>
                        <td>{{$total_general}}</td>
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
