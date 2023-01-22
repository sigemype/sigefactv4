@php
   
    use App\Models\Tenant\Document;
    use App\CoreFacturalo\Helpers\Template\TemplateHelper;
    use App\Models\Tenant\SaleNote;
    use App\Models\Tenant\Catalogs\DocumentType;
    use App\Models\Tenant\Series;

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

$document_types=DocumentType::OnlyAvaibleDocuments()->get();
@endphp
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
    </head>
    <body>
        <div>
            <p align="center" class="title"><strong>Reporte Documentos</strong></p>
        </div>
        <div style="margin-top:20px; margin-bottom:20px;">
            <table>
                <tr>
                    <td>
                        <p><strong>Empresa: </strong>{{$company->name}}</p>
                    </td>
                    <td>
                        <p><strong>Fecha: </strong>{{date('Y-m-d')}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Ruc: </strong>{{$company->number}}</p>
                    </td>
                    <td>
                        <p><strong>Establecimiento: </strong>{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</p>
                    </td>
                </tr>

                @inject('reportService', 'Modules\Report\Services\ReportService')
                <tr>
                    @if($filters['seller_id'])
                    <td>
                        <p><strong>Usuario: </strong>{{$reportService->getUserName($filters['seller_id'])}}</p>
                    </td>
                    @endif
                    @if($filters['person_id'])
                    <td>
                        <p><strong>Cliente: </strong>{{$reportService->getPersonName($filters['person_id'])}}</p>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
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
                    
                    //dd($clear_serie);
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
                        <tr style="background-color: #ebebeb;">
                            <th style="padding: 5px; text-align: center;">#</th>
                        @if ($columns->user_seller->visible)
                            <th style="padding: 5px; text-align: center;" class="">Usuario/Vendedor</th>
                        @endif
                        
                        <th style="padding: 5px; text-align: center;">Tipo Doc</th>
                        <th style="padding: 5px; text-align: center;">Serie</th>
                        <th style="padding: 5px; text-align: center;">Número</th>
                        <th style="padding: 5px; text-align: center;">Fecha emisión</th>
                        <th style="padding: 5px; text-align: center;">Fecha Vencimiento</th>
                        @if ($columns->doc_affect->visible)
                            <th style="padding: 5px; text-align: center;">Doc. Afectado</th>
                        @endif
                        
                        @if ($columns->guides->visible)
                            <th style="padding: 5px; text-align: center;"># Guía</th>
                        @endif
                        @if ($columns->quote->visible)
                            <th style="padding: 5px; text-align: center;">Cotización</th>
                        @endif
                        @if ($columns->case->visible)
                            <th style="padding: 5px; text-align: center;">Caso</th>
                        @endif
                        
                        @if ($columns->district->visible)
                        <th style="padding: 5px; text-align: center;">DIST</th>
                        @endif
                        @if ($columns->department->visible)
                        <th style="padding: 5px; text-align: center;">DPTO</th>
                        @endif
                        @if ($columns->province->visible)
                        <th style="padding: 5px; text-align: center;">PROV</th>
                        @endif
                        @if ($columns->client_direction->visible)
                        <th style="padding: 5px; text-align: center;">Direccion de cliente</th>
                        @endif
                        <th style="padding: 5px; text-align: center;">Cliente</th>
                        @if ($columns->ruc->visible)
                        <th style="padding: 5px; text-align: center;">RUC</th>
                        @endif
                        
                        <th style="padding: 5px; text-align: center;">Estado</th>
                        @if ($columns->currency_type_id->visible)
                            <th class="">Moneda</th>
                        @endif
                        
                        @if ($columns->web_platforms->visible)
                        <th style="padding: 5px; text-align: center;">Plataforma</th>
                        @endif
                        @if ($columns->purchase_order->visible)
                            <th style="padding: 5px; text-align: center;">Orden de compra</th>
                        @endif
                        
                        @if ($columns->note_sale->visible)
                        <th style="padding: 5px; text-align: center;">Nota de venta</th>
                        @endif
                        @if ($columns->date_note->visible)
                        <th style="padding: 5px; text-align: center;">Fecha N. Venta</th>
                        @endif
                        @if ($columns->payment_form->visible)
                        <th style="padding: 5px; text-align: center;" class="">Forma de pago</th>
                        @endif
                        @if ($columns->payment_method->visible)
                        <th style="padding: 5px; text-align: center;"> MÉTODO DE PAGO </th>
                        @endif
                        @if ($columns->total_charge->visible)
                        <th style="padding: 5px; text-align: center;">Total Cargos</th>
                        @endif
                        @if ($columns->total_exonerated->visible)
                            <th style="padding: 5px; text-align: center;">Total Exonerado</th>
                        @endif
                        @if ($columns->total_unaffected->visible)
                            <th style="padding: 5px; text-align: center;">Total Inafecto</th>
                        @endif
                        @if ($columns->total_free->visible)
                            <th style="padding: 5px; text-align: center;">Total Gratuito</th>
                        @endif
                        @if ($columns->total_taxed->visible)
                            <th style="padding: 5px; text-align: center;">Total Gravado</th>
                        @endif
                        
                        <th style="padding: 5px; text-align: center;">Descuento total</th>
                        @if ($columns->total_igv->visible)
                            <th style="padding: 5px; text-align: center;">Total IGV</th>
                        @endif
                        
                        @if ($columns->total_isc->visible)
                        <th style="padding: 5px; text-align: center;">Total ISC</th>
                        @endif
                        @if ($columns->total->visible)
                            <th style="padding: 5px; text-align: center;">Total</th>
                        @endif
                        
                        @if ($columns->items->visible)
                            <th style="padding: 5px; text-align: center;">Total de productos</th>
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
                                <td style="padding: 5px; text-align: center;" class="celda">{{$t+1}}</td>
                                @if ($columns->user_seller->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">
                                    @if($filters['user_type']==='CREADOR')
                                        {{$userCreator}}
                                    @else
                                        {{$user}}
                                    @endif
                                </td>
                                @endif
                                <td style="padding: 5px; text-align: center;" class="celda">{{$document_type->id}}</td>
                                <td style="padding: 5px; text-align: center;" class="celda">{{$value->series}}</td>
                                <td style="padding: 5px; text-align: center;" class="celda">{{$value->number}}</td>
                                <td style="padding: 5px; text-align: center;" class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>
                                <td style="padding: 5px; text-align: center;" class="celda">{{isset($value->invoice) ? $value->invoice->date_of_due->format('Y-m-d'):''}}</td>
                                @if(in_array($document_type->id,["07","08"]) && $value->note)
        
                                    @php
                                        $serie = ($value->note->affected_document) ? $value->note->affected_document->series : $value->note->data_affected_document->series;
                                        $number =  ($value->note->affected_document) ? $value->note->affected_document->number : $value->note->data_affected_document->number;
                                        $serie_affec = $serie.' - '.$number;
        
                                    @endphp
        
        
                                @endif
                                @if ($columns->doc_affect->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{$serie_affec }} </td>
                                @endif
                                
                                @if ($columns->guides->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">
                                    @if(!empty($value->guides))
                                        @foreach($value->guides as $guide)
                                            {{ $guide->number }}<br>
                                        @endforeach
                                    @endif
                                </td>
                                @endif
                                @if ($columns->quote->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{ ($value->quotation) ? $value->quotation->number_full : '' }}</td>
                                @endif
                                @if ($columns->case->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{ isset($value->quotation->sale_opportunity) ? $value->quotation->sale_opportunity->number_full : '' }}</td>
                                @endif
                                
        
                                <?php $stablihsment = \App\CoreFacturalo\Helpers\Template\ReportHelper::getLocationData($value); ?>
                                @if ($columns->district->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">{{$stablihsment['district']}}</td>
                                @endif
                                @if ($columns->department->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">{{$stablihsment['department']}}</td>
                                @endif
                                @if ($columns->province->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">{{$stablihsment['province']}}</td>
                                @endif
                                @if ($columns->client_direction->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">{{$value->customer->address}}</td>
                                @endif
                                <td style="padding: 5px; text-align: center;" class="celda">{{$value->customer->name}}</td>
                                @if ($columns->ruc->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">{{$value->customer->number}}</td>
                                @endif
                                <td style="padding: 5px; text-align: center;" class="celda">{{$value->state_type->description}}</td>
        
                                @php
                                    $signal = $document_type->id;
                                    $state = $value->state_type_id;
                                @endphp
                                @if ($columns->currency_type_id->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{$value->currency_type_id}}</td>
                                @endif
                                
                                @if ($columns->web_platforms->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">
                                    @foreach ($value->getPlatformThroughItems() as $platform)
                                        <label class="d-block">{{$platform->name}}</label>
                                    @endforeach
                                </td>
                                @endif
                                @if ($columns->purchase_order->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{$value->purchase_order}}</td>
                                @endif
                                
        
                                @if($value->sale_note)
                                    @if ($columns->note_sale->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{ $value->sale_note->number_full }}</td>
                                    @endif
                                    @if ($columns->date_note->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{ $value->sale_note->date_of_issue->format('Y-m-d') }}</td>
                                    @endif
                                @else
                                    @if ($columns->note_sale->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda"></td>
                                    @endif
                                    @if ($columns->date_note->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda"></td>
                                    @endif
                                @endif
                                @if ($columns->payment_form->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">
                                    {{ ($value->payments()->count() > 0) ? $value->payments()->first()->payment_method_type->description : ''}}
                                </td>
                                @endif
                                @if ($columns->payment_method->visible)
                                <td style="padding: 5px; text-align: center;" class="celda">
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
        
                            {{-- <!-- <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_exonerated}} </td>
                                        <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_unaffected}}</td>
                                        <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_free}}</td>
        
                                        <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_taxed}}</td>
        
                                        <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_igv}}</td>
                                        <td class="celda">{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total}}</td> --> --}}
        
                                @if($signal == '07')
        
                                    @if(in_array($value->state_type_id,['09','11']))
                                        @if ($columns->total_charge->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                        @endif
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                            @if ($columns->total_isc->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                            @endif
                                            <td style="padding: 5px; text-align: center;" class="celda">0</td>
                                        
                                    @else
                                        @if ($columns->total_charge->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_charge}}</td>
                                        @endif
                                        @if ($columns->total_exonerated->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_exonerated}}</td>
                                        @endif
                                        @if ($columns->total_unaffected->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_unaffected}}</td>
                                        @endif
                                        @if ($columns->total_free->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_free}}</td>
                                        @endif
                                        @if ($columns->total_taxed->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_taxed}}</td>
                                        @endif
                                        
                                        <td style="padding: 5px; text-align: center;" class="celda">{{$value->total_discount}}</td>
                                        @if ($columns->total_igv->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_igv}}</td>
                                        @endif
                                        
                                        @if ($columns->total_isc->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total_isc}}</td>
                                        @endif
                                        @if ($columns->total->visible)
                                            <td style="padding: 5px; text-align: center;" class="celda">{{$signal == '07' ? "-" : ""  }}{{$value->total}}</td>
                                        @endif
                                        
                                    @endif
        
                                @else
                                    @if ($columns->total_charge->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_charge}}</td>
                                    @endif
                                    @if ($columns->total_exonerated->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_exonerated}}</td>
                                    @endif
                                    
                                    @if ($columns->total_unaffected->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_unaffected}}</td>
                                    @endif
                                    
                                    @if ($columns->total_free->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_free}}</td>
                                    @endif
                                    
                                    @if ($columns->total_taxed->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed}}</td>
                                    @endif
                                    
                                    <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_discount}}</td>
                                    @if ($columns->total_igv->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv}}</td>
                                    @endif
                                    
                                    @if ($columns->total_isc->visible)
                                    <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_isc}}</td>
                                    @endif
                                    @if ($columns->total->visible)
                                        <td style="padding: 5px; text-align: center;" class="celda">{{ (in_array($document_type->id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total}}</td>
                                    @endif
                                    
        
                                @endif
                                @php
        
                                    $value->total_exonerated = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_exonerated;
                                    $value->total_unaffected = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_unaffected;
                                    $value->total_free = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_free;
        
                                    $value->total_taxed = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed;
                                    $value->total_igv = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv;
                                    $value->total = (in_array($document_type->id,['01','03', '07']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total;
                                @endphp
        
                                @php
        
                                    $serie_affec =  '';
        
                                    $quality_item=0;
                                    foreach ($value->items as $itm) {
                                        $quality_item+=$itm->quantity;
                                    }
        
                                @endphp
                                @if ($columns->items->visible)
                                    <td style="padding: 5px; text-align: center;">{{$quality_item}}</td>
                                @endif
                            </tr>
                            @php
                                if($value->currency_type_id == 'PEN'){
        
        
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
                            <td colspan="{{$col_num}}"></td>
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
                <table>
                    <thead>
                        <tr style="background-color: #ebebeb;">
                            <th style="padding: 5px; text-align: center;">DOC</th>
                            <th style="padding: 5px; text-align: center;">SERIE</th>
                            <th style="padding: 5px; text-align: center;">TOTAL</th>
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
                                        <td style="padding: 5px; text-align: center;">{{$document['description']}}</td>
                                        <td style="padding: 5px; text-align: center;">{{$serie['number']}}</td>
                                        <td style="padding: 5px; text-align: center;">{{$serie['total']}}</td>
                                    </tr>
                                    @php
                                        $total_general=$total_general+$serie['total'];
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                        <tr >
                            <td style="padding: 5px; text-align: center;" colspan="2">TOTAL GENERAL</td>
                            <td style="padding: 5px; text-align: center;">{{$total_general}}</td>
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
