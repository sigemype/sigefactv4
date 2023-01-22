@php
    /**
     * @var $company Modules\Facturalo\Entities\Company\CompanyEntity
     * @var $document Modules\Document\Entities\Guide\GuideEntity
     * @var $items Modules\Document\Entities\Guide\GuideItemEntity
     */
    $company = $document->company;
    $items = $document->items;
@endphp
<table class="table-items">
    <thead>
    <tr>
        <th class="t-text-left t-bg-head t-color-line-bottom t-color-head-text">CÓDIGO</th>
        <th class="t-text-left t-bg-head t-color-line-bottom t-color-head-text">DESCRIPCIÓN</th>
        <th class="t-text-right t-bg-head t-color-line-bottom t-color-head-text">CANT</th>
{{--        <th class="t-text-center t-bg-head t-color-line-bottom t-color-head-text">UNID</th>--}}
        <th class="t-text-right t-bg-head t-color-line-bottom t-color-head-text">COSTO</th>
        <th class="t-text-right t-bg-head t-color-line-bottom t-color-head-text">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $row)
        <tr>
            <td class="t-color-line-table t-py-3 t-text-left">{{ $row->internal_id }}</td>
            <td class="t-color-line-table t-py-3 t-text-left">{!! $row->name !!}</td>
            <td class="t-color-line-table t-py-3 pl-10 t-text-right">{{ $row->quantity }}</td>
            <td class="t-color-line-table t-py-3 pl-10 t-text-right">{{ floatval($row->unit_cost) }}</td>
            <td class="t-color-line-table t-py-3 pl-10 t-text-right">{{ floatval($row->total) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

