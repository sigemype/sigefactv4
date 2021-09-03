<?php

use App\Models\Tenant\ItemSet;

$purchseOrder = $document->purchase_order;
$stablihsment = $stablihsment ?? [
        'district' => '',
        'department' => '',
        'province' => '',
    ];
$discount = '';
$unit_price = '';
$unit_value = '';
$total = '';
$total_value = '';
$total_item_purchase = number_format($total_item_purchase, 2);
$utility_item = number_format($utility_item, 2);
$relation_item = $value->relation_item;
$web_platform = '';
$purchase_unit_price = '';
$igv = '';
$system_isc_type_id = '';
$total_isc = '';
$total_plastic_bag_taxes = '';
$pack_prefix = '';
$pack_price_prefix = '';
if (!isset($qty)) {
    /** Item normal */
    $discount = $value->total_discount;
    $unit_price = $value->unit_price;
    $unit_value = $value->unit_value;
    $total = $value->total;
    $total_value = $value->total_value;
    $web_platform = optional($relation_item->web_platform)->name;
    $purchase_unit_price = ($relation_item) ? $relation_item->purchase_unit_price : 0;
    $igv = $value->system_isc_type_id;
    $total_isc = $value->total_isc;
    $system_isc_type_id = $value->system_isc_type_id;
    $total_plastic_bag_taxes = $value->total_plastic_bag_taxes;
    $category = $relation_item->category->name;
    $brand = $relation_item->brand->name;
} else {
    /** Item desde un pack */
    $item = \App\Models\Tenant\Item::find($item->id);
    $pack_prefix = "(Item de pack) - ";
    $pack_price_prefix = "(pck) ";
    $unit_price = $item->sale_unit_price;
    $utility_item = '';
    $relation_item = $item;
    $purchase_unit_price = ($relation_item) ? $relation_item->purchase_unit_price : 0;
    $total =number_format( $qty * (float)$purchase_unit_price,2);
    /*
    $set = ItemSet::where('individual_item_id',$item->id)->first();
    if($set !== null){
         $qty = $set->quantity;
    }
    */
    $item_web_platform = $item->getWebPlatformModel();
    if ($item_web_platform) {
        $web_platform = $item_web_platform->name;
    }
    $category = $item->category->name;
    $brand = $item->brand->name;
}
// Se debe pasar al modelo
$qty = $qty ?? $value->quantity;
$isSaleNote = ($document_type_id != '80' && $type == 'sale') ? true : false;
?>
<tr>
    <td class="celda">{{ $document->date_of_issue->format('Y-m-d') }}</td>
    @if($isSaleNote)
        <td class="celda">{{ $stablihsment['district'] }}</td>
        <td class="celda">{{ $stablihsment['department'] }}</td>
        <td class="celda">{{ $stablihsment['province'] }}</td>
    @endif
    <td class="celda">
        @if($isSaleNote)
            {{ $document->document_type->description }}
        @else
            NOTA DE VENTA
        @endif
    </td>
    <td class="celda">
        @if($isSaleNote)
            {{ $document->document_type_id }}
        @else
            80
        @endif
    </td>
    <td class="celda">{{ $document->series }}</td>
    <td class="celda">{{ $document->number }}</td>
    <td class="celda">{{ $purchseOrder }}</td>
    <td class="celda">{{ $web_platform }}</td>
    <td class="celda">{{ $document->state_type_id == '11' ? 'SI':'NO' }}</td>
    <td class="celda">{{ $document->customer->identity_document_type->description }}</td>
    <td class="celda">{{ $document->customer->number }}</td>
    <td class="celda">{{ $document->customer->name }}</td>
    <td class="celda">
        @if($isSaleNote)
            {{ $document->seller_id == null ? $document->user->name : $document->seller->name }}
        @else
            {{$document->user->name}}
        @endif
    </td>
    <td class="celda">{{ $document->currency_type_id }}</td>
    <td class="celda">{{ $document->exchange_rate_sale }}</td>
    <td class="celda">
        @if($isSaleNote)
            {{ $item->unit_type_id }}
        @else
            {{$relation_item->unit_type->description}}
        @endif
    </td>
    <td class="celda">
        @if($isSaleNote)
            {{ $item->internal_id }}
        @else
            {{$relation_item->internal_id}}
        @endif
    </td>
    <td class="celda">{{ $pack_prefix }}{{ $item->description }}</td>
    <td class="celda">{{ $qty }}</td>
    <td class="celda">{{ $series }}</td>
    <td class="celda">{{ $model }}</td>
    <td class="celda">{{(!empty($purchase_unit_price)?$pack_price_prefix:'')}}{{ $purchase_unit_price }}</td>
    <td class="celda">{{ $unit_value }}</td>
    <td class="celda">{{ $unit_price }}</td>
    <td class="celda">{{ $discount }}</td>
    <td class="celda">{{ $total_value }}</td>
    <td class="celda">{{ $value->affectation_igv_type_id }}</td>
    <td class="celda">{{ $igv }}</td>
    <td class="celda">{{ $system_isc_type_id }}</td>
    <td class="celda">{{ $total_isc }}</td>
    <td class="celda">{{ $total_plastic_bag_taxes }}</td>
    <td class="celda">{{(!empty($total)?$pack_price_prefix:'')}}{{ $total }}</td>
    <td class="celda">{{(!empty($total_item_purchase)?$pack_price_prefix:'')}}{{ $total_item_purchase }}</td>
    <td class="celda">{{ $utility_item }}</td>
    <td class="celda">{{ $brand }}</td>
    <td class="celda">{{ $category }}</td>
</tr>
