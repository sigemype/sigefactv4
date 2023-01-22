@php
    use App\CoreFacturalo\Helpers\Template\ReportHelper;
@endphp

<table>
    
    <thead>
        <tr>
            <th colspan="5">Totales por producto</th>
        </tr>
        <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th style="text-align: right">Último V. Unitario</th>
            <th style="text-align: right">Total</th>
        </tr>
    </thead>
    <tbody>

        @php
            $sold_items = $documents->whereIn('record_type', ['sale_note_item', 'document_item'])->groupBy('item_id');

            $table_sold_items = $sold_items->map(function($row, $key){

                $last_item = $row->last();
                $description = $last_item['item']->description ?? 'Error al obtener nombre de producto.';
                $unit_value = $last_item['unit_value'];
                $quantity = $row->sum('quantity');
                $total = $row->sum('total');

                return [
                    'item_id' => $last_item['item_id'],
                    'description' => $description,
                    'quantity' => $quantity,
                    'unit_value' => $unit_value,
                    'total' => $total,
                ];
            });

        @endphp

        @foreach ($table_sold_items as $item)
            <tr>
                <td class="celda">{{ $loop->iteration }}</td>
                <td class="celda">{{ $item['description'] }}</td>
                <td class="celda">{{ $item['quantity'] }}</td>
                <td class="celda" style="text-align: right">{{ ReportHelper::setNumber($item['unit_value'], 2, '.', '') }}</td>
                <td class="celda" style="text-align: right">{{ ReportHelper::setNumber($item['total'], 2, '.', '') }}</td>
            </tr>
        @endforeach

        <tr>
            <td class="celda" colspan="3"></td>
            <td class="celda"> Totales </td>
            <td class="celda" style="text-align: right">
                {{ ReportHelper::setNumber($table_sold_items->sum('total'), 2, '.', '') }}
            </td>
        </tr>
    </tbody>
</table>