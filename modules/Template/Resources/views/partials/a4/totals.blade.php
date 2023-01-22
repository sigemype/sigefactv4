<table class="full-width table-totals">
    @if($document->total_exportation > 0)
        <tr>
            <td colspan="8" class="text-right font-bold">OP.
                EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
        </tr>
    @endif
    @if($document->total_free > 0)
        <tr>
            <td colspan="8" class="text-right font-bold">OP.
                GRATUITAS: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
        </tr>
    @endif
    @if($document->total_unaffected > 0)
        <tr>
            <td colspan="8" class="text-right font-bold">OP.
                INAFECTAS: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
        </tr>
    @endif
    @if($document->total_exonerated > 0)
        <tr>
            <td colspan="8" class="text-right font-bold">OP.
                EXONERADAS: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
        </tr>
    @endif
    @if($document->total_taxed > 0)
        <tr>
            <td colspan="8" class="text-right font-bold">OP.
                GRAVADAS: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
        </tr>
    @endif
    @if($document->total_discount > 0)
        <tr>
            <td colspan="8"
                class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}
                : {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
        </tr>
    @endif
    @if($document->total_plastic_bag_taxes > 0)
        <tr>
            <td colspan="8" class="text-right font-bold">ICBPER: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
        </tr>
    @endif
    <tr>
        <td colspan="8" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
        <td class="text-right font-bold" style="width: 70px">{{ number_format($document->total_igv, 2) }}</td>
    </tr>
    @if($document->total_charge > 0)
        @php
            $total_factor = 0;
            foreach($document->charges as $charge) {
                $total_factor = ($total_factor + $charge->factor) * 100;
            }
        @endphp
        <tr>
            <td colspan="8" class="text-right font-bold">CARGOS ({{$total_factor}}
                %): {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_charge, 2) }}</td>
        </tr>
    @endif

    @if($document->perception)
        <tr>
            <td colspan="8" class="text-right font-bold"> IMPORTE
                TOTAL: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="8" class="text-right font-bold">
                PERCEPCIÓN: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->perception->amount, 2) }}</td>
        </tr>
        <tr>
            <td colspan="8" class="text-right font-bold">TOTAL A
                PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format(($document->total + $document->perception->amount), 2) }}</td>
        </tr>
    @else
        <tr>
            <td colspan="8" class="text-right font-bold border-top">TOTAL A
                PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold border-top">{{ number_format($document->total, 2) }}</td>
        </tr>
    @endif

    @if($balance < 0)
        <tr>
            <td colspan="8" class="text-right font-bold">VUELTO: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format(abs($balance),2, ".", "") }}</td>
        </tr>
    @endif
</table>
