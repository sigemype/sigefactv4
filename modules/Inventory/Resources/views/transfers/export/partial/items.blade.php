<div class="">
    <div class=" ">
        <table class="full-width">
            <?php

            use App\Models\Tenant\Company;
            use App\Models\Tenant\Configuration;
            use App\Models\Tenant\User;
            use Modules\Inventory\Models\Inventory;
            use Modules\Inventory\Models\InventoryTransfer;
            use Modules\Inventory\Models\Warehouse;
            use Illuminate\Support\Carbon;
            use Illuminate\Database\Eloquent\Collection;

            $motivo = !empty($data['motivo']) ? $data['motivo'] : null;
            /** @var Carbon $created_at */
            /** @var Warehouse $warehouse_to */
            /** @var Warehouse $warehouse_from */
            /** @var User $user */
            /** @var Collection|Inventory[] $inventories */

            $created_at = !empty($data['created_at']) ? $data['created_at'] : Carbon::now();
            $quantity = !empty($data['quantity']) ? $data['quantity'] : 0;

            $warehouse_from = !empty($data['warehouse_from']) ? $data['warehouse_from'] : new Warehouse();
            $warehouse_to = !empty($data['warehouse_to']) ? $data['warehouse_to'] : new Warehouse();
            $user = !empty($data['user']) ? $data['user'] : new User();
            $configuration = !empty($data['configuration']) ? $data['configuration'] : new Configuration();
            $company = !empty($data['company']) ? $data['company'] : new Company();

            $pdf = $pdf ?? false;


            ?>


            <thead>
            <tr>
                <th class="five-width text-center">ITEM</th>
                <th class="ten-width text-left">CODIGO INTERNO</th>
                <th class="fourteen-width text-left">DESCRIPCIÓN PRODUCTO</th>
                <th class="ten-width">UNIDAD</th>
                <th class="ten-width">CANTIDAD</th>
                <th class="ten-width">LOTE/SERIE</th>
                <!--        <th width="10%">SERIE</th>-->
            </tr>
            </thead>
            <tbody>
            @foreach ($inventories as $index => $inventory)
                <?php
                // dd($inventory->inventories_transfer);
                /** @var \Modules\Inventory\Models\Inventory $inventory */
                $item = $inventory->item;
                $itemCollection = $item->getCollectionData($configuration);

                $itemCollection['description'] = substr($itemCollection['description'], 0, 49);
                $itemCollection['internal_id'] = substr($itemCollection['internal_id'], 0, 10);
                $itemCollection['unit_type_text'] = substr($itemCollection['unit_type_text'], 0, 10);

                $item_transfers = !empty($data['item_transfers']) ? $data['item_transfers'] : null;
                $lots = $item_transfers->filter(function($value) use ($item) {
                    return $value['item_id'] == $item->id;
                });

                $qty = $inventory->quantity;
                $lot_code = $inventory->lot_code;
                /*
                @todo BUSCAR DONDE SE GUARDA LA SERIE en modules/Inventory/Http/Controllers/TransferController.php 237
                */
                // $is_serie = (bool)$inventory->item->series_enabled;
                // $serie = $inventory->item->item_lots->where('updated_at', $inventory->inventories_transfer->created_at)->first();
                ?>
                <tr>
                    <td class="celda text-center">{{$index + 1}}</td>
                    <td class="celda text-left">{{$itemCollection['internal_id']}}</td>
                    <td class="celda text-left">{{$itemCollection['description']}}</td>
                    <td class="celda">{{$itemCollection['unit_type_text']}}</td>
                    <td class="celda">{{$qty}}</td>
                    <td class="celda">
                        @foreach($lots->unique('code') as $lot)
                            {{ $lot['code'] }}
                            @if(!$loop->last)
                                <br>
                            @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
</div>

