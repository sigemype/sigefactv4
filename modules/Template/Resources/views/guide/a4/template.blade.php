@php
    /**
     * @var $document Modules\Inventory\Entities\Guide\GuideEntity
     * @var $establishment Modules\Inventory\Entities\Company\EstablishmentEntity
     */
    $establishment = $document->establishment;
@endphp
<div class="t-block">
    <div class="t-row">
        @include('template::headers.'.$size.'.'.$template_id)
    </div>
    <div class="t-row t-py-20">
        <table class="t-pr-30">
            <tr>
                <td class="t-fs-md" style="width: 120px;">Fecha de emisión:</td>
                <td class="t-fs-md">{{ $document->date_of_issue->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="t-fs-md">Establecimiento:</td>
                <td class="t-fs-md">{{ $establishment->name }}</td>
            </tr>
            <tr>
                <td class="t-fs-md">Motivo:</td>
                <td class="t-fs-md">{{ $document->inventory_operation_name }}</td>
            </tr>
            <tr>
                <td class="t-fs-md">Usuario:</td>
                <td class="t-fs-md">{{ $document->user_name }}</td>
            </tr>
        </table>
    </div>
    <div class="t-row t-pb-20">
        @include('template::'.$model.'.'.$size.'.partials.items')
    </div>
    @include('template::partials.'.$size.'.observations')
</div>
