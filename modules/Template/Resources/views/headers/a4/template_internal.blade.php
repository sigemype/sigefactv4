@php
    /**
     * @var $document
     * @var $company Modules\Facturalo\Entities\Company\CompanyEntity
     * @var $establishment Modules\Facturalo\Entities\Company\EstablishmentEntity
     */
    $company = $document->company;
    $establishment = $document->establishment;
    $establishment_main = property_exists($document, 'establishment_main')?$document->establishment_main:null;
@endphp
<table>
    <tr>
        <td style="vertical-align: top">
            @if($company->trade_name)
                <div class="t-row header-font-12pt t-text-left">{{ $company->trade_name }}</div>
            @endif
                <div class="t-row header-font-12pt t-text-left">{{ $company->name }}</div>
                <div class="t-row header-font-10pt t-text-left">RUC {{ $company->number }}</div>
            @if($establishment->print_header_text)
                <div class="t-row header-font-7pt t-text-left">{!! $establishment->print_header_text !!}</div>
            @endif
            @if($establishment_main)
                <div class="t-row header-font-7pt t-text-left">{{ $establishment_main->address }}, {{ $establishment_main->location_name }}</div>
            @endif
            <div class="t-row header-font-7pt t-text-left">{{ $establishment->address }}, {{ $establishment->location_name }}</div>
        </td>
    </tr>
    <tr>
        <td class="t-color-line-bottom">
            <table class="t-pb-5 t-pt-20">
                <tr>
                    <td class="t-fs-xxxl">{{ $document->document_type_name }}</td>
                    <td class="t-fs-xxl t-text-right">{{ $document->series.' - '.$document->number }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
