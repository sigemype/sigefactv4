<?php

namespace Modules\Item\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Item\Models\ItemLotsGroup;
use App\Models\Tenant\Item;


class ItemLotsGroupController extends Controller
{
    
    /**
     * 
     * Obtener lotes disponibles del item, para ventas
     *
     * @param  int $item_id
     * @return array
     */
    public function getAvailableItemLotsGroup($item_id)
    {
        return ItemLotsGroup::where('item_id', $item_id)
                            ->get()
                            ->transform(function ($row) {
                                return $row->getRowResourceSale();
                            });
    }


}
