<?php
?>
<tr>
    <td class="celda"> {{$purchase->date_of_issue->format('Y-m-d')}}</td>
    {{--
    @if($isSaleNote)
    <td class="celda">{{ $stablihsment['district'] }}</td>
    <td class="celda">{{ $stablihsment['department'] }}</td>
    <td class="celda">{{ $stablihsment['province'] }}</td>
    @endif
    --}}
    <td class="celda"> {{$purchase->document_type->description}}</td>
    <td class="celda"> {{$purchase->document_type_id}}</td>
    <td class="celda"> {{$purchase->series}}</td>
    <td class="celda"> {{$purchase->number}}</td>
    {{--
    <td class="celda">{{ $purchseOrder }}</td>
    <td class="celda">{{ $web_platform }}</td>
    --}}
    <td class="celda"> {{$purchase->state_type_id == '11' ? 'SI':'NO'}}</td>
    <td class="celda"> {{$purchase->supplier->identity_document_type->description}}</td>
    <td class="celda"> {{$purchase->supplier->number}}</td>
    <td class="celda"> {{$purchase->supplier->name}}</td>
    {{--
    <td class="celda">
    @if($isSaleNote)
    {{ $document->seller_id == null ? $document->user->name : $document->seller->name }}
    @else
    {{$document->user->name}}
    @endif
    </td>
    --}}
    <td class="celda"> {{$purchase->currency_type_id}}</td>
    <td class="celda"> {{$purchase->exchange_rate_sale}}</td>
    <td class="celda"> {{$value->item->unit_type_id}}</td>
    <td class="celda"> {{$value->relation_item ? $value->relation_item->internal_id:''}}</td>
    <td class="celda"> {{$value->item->description}}</td>
    <td class="celda"> {{$value->quantity}}</td>
    <td class="celda"></td>
    {{--
    <td class="celda">{{ $model }}</td>
    --}}
    <td class="celda"></td>
    <td class="celda"> {{$value->unit_value}}</td>
    <td class="celda"> {{$value->unit_price}}</td>
    <td class="celda">
        @if($value->discounts)
            {{collect($value->discounts)->sum('amount')}}
        @endif
    </td>
    <td class="celda"> {{$value->total_value}}</td>
    <td class="celda"> {{$value->affectation_igv_type_id}}</td>
    <td class="celda"> {{$value->total_igv}}</td>
    <td class="celda"> {{$value->system_isc_type_id}}</td>
    <td class="celda"> {{$value->total_isc}}</td>
    <td class="celda"> {{$value->total_plastic_bag_taxes}}</td>
    <td class="celda"> {{$value->total}}</td>
    {{--
    <td class="celda">{{ $total_item_purchase }}</td>
    <td class="celda">{{ $utility_item }}</td>
    <td class="celda">{{ $brand }}</td>
    <td class="celda">{{ $category }}</td>
    --}}
</tr>
