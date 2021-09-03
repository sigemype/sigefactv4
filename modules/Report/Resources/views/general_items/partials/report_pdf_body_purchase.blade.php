<?php
?>
<tr>
    <td class="celda">{{$document->date_of_issue->format('Y-m-d')}}</td>
    {{--
    @if($isSaleNote)
        <td class="celda">{{ $stablihsment['district'] }}</td>
        <td class="celda">{{ $stablihsment['department'] }}</td>
        <td class="celda">{{ $stablihsment['province'] }}</td>
    @endif
    --}}
    <td class="celda">{{$document->series}}</td>
    <td class="celda">{{$document->number}}</td>
    {{--
    @if( $type == 'sale')
        <td class="celda">{{ $purchseOrder }} </td>
    @endif
    --}}
    <td class="celda">{{$document->supplier->identity_document_type_id}}</td>
    <td class="celda">{{$document->supplier->number}}</td>
    <td class="celda">{{$document->supplier->name}}</td>
    <td class="celda">{{$document->currency_type_id}}</td>
    {{-- <td class="celda">{{$document->exchange_rate_sale}}</td> --}}
    <td class="celda">{{$value->item->unit_type_id}}</td>

    {{-- <td class="celda">{{$value->relation_item ? $value->relation_item->internal_id:''}}</td> --}}
    <td class="celda">{{$value->relation_item->brand->name}}</td>
    {{--
    @if($type == 'sale')
        <td class="celda">{{ $model }}</td>
    @endif
    --}}
    <td class="celda">{{$value->item->description}}</td>
    <td class="celda">{{$value->relation_item->category->name}}</td>
    <td class="celda">{{number_format($value->quantity, 2)}}</td>
    <td class="celda">{{number_format($value->unit_price, 2)}}</td>
    <td class="celda">{{number_format($value->total, 2)}}</td>
    {{-- <td class="celda"></td> --}}

</tr>
